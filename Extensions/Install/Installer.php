<?php

namespace App\Extensions\Install;

use Exception;
use Simflex\Core\Console\Command;
use Simflex\Core\ConsoleBase;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Log;
use Simflex\Core\Models\Migration;

class Installer extends ConsoleBase
{
    protected const DIRECTORIES = [
        'cache',
        'cache/css',
        'uf/files',
        'uf/images',
    ];

    #[Command('Installs the framework')]
    public function install(): void
    {
        $this->initDir('uf', false);
        $this->initDir('uf/log', false);

        $config = Container::getConfig();
        Log::info('Testing database connection');

        // check if database works
        try {
            if (!DB::connect()) {
                $this->initEnv();
            }
        } catch (Exception) {
            // init env if exception was thrown.
            $this->initEnv();
        }

        // force reconnect
        DB::connect();

        Log::info('Checking if database is initialized');

        // test if we need to init database
        $res = DB::result(
            'SELECT COUNT(*) c FROM information_schema.TABLES WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?',
            'c',
            [
                'migration',
                $config->db['name']
            ]
        );

        if (!$res) {
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

        // initialize environment
        $config = Container::getConfig();
        $config->db['host'] = readline('* Database host: ');
        $config->db['user'] = readline('* Database user: ');
        $config->db['password'] = readline('* Database password: ');
        $config->db['name'] = readline('* Database name: ');

        // write to .env
        file_put_contents(
            SF_ROOT_PATH . '/.env',
            implode("\n", [
                "DB_HOST={$config->db['host']}",
                "DB_USER={$config->db['user']}",
                "DB_PASS={$config->db['password']}",
                "DB_NAME={$config->db['name']}",
                "DEVELOPER=1", // enabled by default
            ])
        );
    }

    protected function initDatabase(): void
    {
        Log::info('Initializing database');

        // check if /database/migrations/00_migration.php exists
        if (!file_exists(SF_ROOT_PATH . '/database/migrations/00_migration.php')) {
            Log::critical('Core migration file not found. Please check your installation.');
            exit;
        }

        /** @var DB\Migration $init */
        $init = include SF_ROOT_PATH . '/database/migrations/00_migration.php';
        if (!$init) {
            Log::critical('Corrupted core migration file. Please check your installation.');
            exit;
        }

        $schema = new DB\Schema();
        $init->up($schema);

        try {
            if (!$schema->commit()) {
                Log::critical('Failed to initialize database');
                exit;
            }

            // write to migration table
            Migration::insertStatic([
                'file' => '00_migration',
            ]);

            // run migrations normally
            $migrator = new DB\Cli\Migrator();
            $migrator->up();

            // run seeders as well
            $seeder = new DB\Cli\Seeder();
            $seeder->run();
        } catch (Exception $e) {
            Log::critical('Failed to initialize database ({ex})', ['ex' => $e->getMessage()]);
            exit;
        }
    }

    protected function initDir(string $dir, bool $log = true): void
    {
        if (!is_dir(SF_ROOT_PATH . '/' . $dir)) {
            mkdir(SF_ROOT_PATH . '/' . $dir);
            if ($log) {
                Log::notice('Created directory {dir}', ['dir' => $dir]);
            }
        }

        // set permissions anyway
        chmod(SF_ROOT_PATH . '/' . $dir, 0777);
    }
}