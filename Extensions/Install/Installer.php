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
use Simflex\Core\DB\Migration as CoreMigration;

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

        Log::info('Testing database connection');
        try {
            if (!DB::connect()) {
                throw new \RuntimeException('Подключение к БД не установлено');
            }
        } catch (Exception $exception) {
            // Ошибка подключения не означает, что конфигурацию и
            // секреты нужно пересоздать.
            throw new \RuntimeException(
                'Не удалось подключиться к БД. Проверьте DB_* в окружении; .env не изменён.',
                previous: $exception
            );
        }

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

    protected function initDatabase(): void
    {
        Log::info('Initializing database');

        // проверяем, есть ли /database/migrations/00_migration.php
        if (!file_exists(Container::getPaths()->getRoot('database/migrations/00_migration.php'))) {
            Log::critical('Core migration file not found. Please check your installation.');
            throw new \RuntimeException('Не удалось инициализировать БД');
        }

        /** @var CoreMigration $init */
        $init = include Container::getPaths()->getRoot('database/migrations/00_migration.php');
        if (!$init) {
            Log::critical('Corrupted core migration file. Please check your installation.');
            throw new \RuntimeException('Не удалось инициализировать БД');
        }

        $schema = new Schema();
        $init->up($schema);

        try {
            if (!$schema->commit()) {
                Log::critical('Failed to initialize database');
                throw new \RuntimeException('Не удалось инициализировать БД');
            }

            // пишем в таблицу миграций
            Migration::insert([
                'file' => '00_migration',
            ]);

            // запускаем миграции как обычно, они же пишут начальные
            // данные
            $migrator = new Migrator();
            $migrator->up();
        } catch (Exception $e) {
            Log::critical('Failed to initialize database ({ex})', ['ex' => $e->getMessage()]);
            throw new \RuntimeException('Не удалось инициализировать БД', previous: $e);
        }
    }

    protected function initDir(string $dir, bool $shouldLog = true): void
    {
        if (!is_dir(Container::getPaths()->getRoot($dir))) {
            mkdir(Container::getPaths()->getRoot($dir));
            if ($shouldLog) {
                Log::notice('Created directory {dir}', ['dir' => $dir]);
            }
        }

        // права выставляем в любом случае
        chmod(Container::getPaths()->getRoot($dir), 0777);
    }
}
