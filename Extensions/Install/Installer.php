<?php

namespace App\Extensions\Install;

use Exception;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\ConsoleBase;
use Simflex\Core\System\Container;
use Simflex\Core\DB\DB;
use Simflex\Core\DB\Cli\Migrator;
use Simflex\Core\DB\Schema;
use Simflex\Core\Logger\Log;
use Simflex\Core\Models\Migration;

class Installer extends ConsoleBase
{
    protected const DIRECTORIES = [
        'cache',
        'public/cache',
        'public/uf/files',
        'public/uf/images',
    ];

    #[Command('Installs the framework')]
    public function install(): void
    {
        $this->initDir('public/uf', false);
        $this->initDir('log', false);

        $config = Container::getConfig();
        Log::info('Testing database connection');

        // проверяем, работает ли база
        try {
            if (!DB::connect()) {
                $this->initEnv();
            }
        } catch (Exception) {
            // инициализируем окружение, если было исключение.
            $this->initEnv();
        }

        // принудительно переподключаемся
        DB::connect();

        Log::info('Checking if database is initialized');

        // проверяем, нужно ли инициализировать базу
        if (!DB::hasTable('migration')) {
            $this->initDatabase();
        }

        Log::info('Checking directories');
        foreach (self::DIRECTORIES as $dir) {
            $this->initDir($dir);
        }

        Log::notice('Install finished');
    }

    protected function initEnv(): void
    {
        Log::info('Initializing environment');

        // инициализируем окружение
        $config = Container::getConfig();
        $config->db['host'] = readline('* Database host: ');
        $config->db['user'] = readline('* Database user: ');
        $config->db['password'] = readline('* Database password: ');
        $config->db['name'] = readline('* Database name: ');

        // пишем в .env
        file_put_contents(
            Container::getPaths()->getRoot('.env'),
            implode("\n", [
                "DB_HOST={$config->db['host']}",
                "DB_USER={$config->db['user']}",
                "DB_PASS={$config->db['password']}",
                "DB_NAME={$config->db['name']}",
                'DEVELOPER=1', // включено по умолчанию
            ])
        );
    }

    protected function initDatabase(): void
    {
        Log::info('Initializing database');

        // проверяем, есть ли /database/migrations/00_migration.php
        if (!file_exists(Container::getPaths()->getRoot('database/migrations/00_migration.php'))) {
            Log::critical('Core migration file not found. Please check your installation.');
            exit;
        }

        /** @var \Simflex\Core\DB\Migration $init */
        $init = include Container::getPaths()->getRoot('database/migrations/00_migration.php');
        if (!$init) {
            Log::critical('Corrupted core migration file. Please check your installation.');
            exit;
        }

        $schema = new Schema();
        $init->up($schema);

        try {
            if (!$schema->commit()) {
                Log::critical('Failed to initialize database');
                exit;
            }

            // пишем в таблицу миграций
            Migration::insertStatic([
                'file' => '00_migration',
            ]);

            // запускаем миграции как обычно, они же пишут начальные данные
            $migrator = new Migrator();
            $migrator->up();
        } catch (Exception $e) {
            Log::critical('Failed to initialize database ({ex})', ['ex' => $e->getMessage()]);
            exit;
        }
    }

    protected function initDir(string $dir, bool $log = true): void
    {
        if (!is_dir(Container::getPaths()->getRoot($dir))) {
            mkdir(Container::getPaths()->getRoot($dir));
            if ($log) {
                Log::notice('Created directory {dir}', ['dir' => $dir]);
            }
        }

        // права выставляем в любом случае
        chmod(Container::getPaths()->getRoot($dir), 0777);
    }
}
