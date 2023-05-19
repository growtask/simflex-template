<?php

namespace App\Extensions\Install;

use App\Core\Console\Alert;
use Dotenv\Dotenv;
use Simflex\Core\ConsoleBase;
use Simflex\Core\Container;
use Simflex\Core\DB;

class ConsoleInstall extends ConsoleBase
{
    public function install($reinstall = '')
    {
        // check if database works
        try {
            if (!DB::connect()) {
                $this->initEnv();
            }
        } catch (\Exception $ex) {
            // init env if exception was thrown.
            $this->initEnv();
        }

        // force reconnect.
        DB::connect();

        // test if we need to init database.
        $res = DB::result('SELECT COUNT(*) c FROM information_schema.TABLES WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?', 'c', [
            'struct_table', \Config::$db_name
        ]);

        if ($reinstall || !$res) {
            Alert::text('Initializing database');

            // initialize db.
            DB::query(file_get_contents(SF_ROOT_PATH . '/database/init.sql'));
            if (DB::error()) {
                Alert::error('Failed to initialize database');
                Alert::text(DB::error());
                return;
            }

            Alert::success('Initialized database');
        } else {
            Alert::text('Database already initialized');
        }

        // migrate stuff.
        $migrator = new DB\Migrator();
        $migrator->up();

        Alert::success('Install finished');
    }

    protected function initEnv()
    {
        Alert::text('Initializing .env');

        // database info
        $host = readline('Host (127.0.0.1): ') ?: '127.0.0.1';
        $username = readline('Username: ');
        $password = readline('Password: ');
        $dbName = readline('Database: ');

        // additional settings
        $devMode = strtolower(readline("Enable developer mode? (\033[1mYes\033[0m/No): ")) ?: 'yes';

        // write .env
        file_put_contents('.env', implode("\n", [
            'DB_HOST=' . $host,
            'DB_USER=' . $username,
            'DB_PASS=' . $password,
            'DB_NAME=' . $dbName,
            'DEVELOPER=' . ($devMode == 'yes' ? '1' : '0')
        ]));

        Alert::success('Config was written to .env');

        // reload config.
        \Config::$db_host = $host;
        \Config::$db_name = $dbName;
        \Config::$db_user = $username;
        \Config::$db_pass = $password;
        \Config::$devMode = $devMode == 'yes';

        Alert::text('Reloaded config');
    }
}