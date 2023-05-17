<?php
namespace App\Extensions\Install;

use App\Core\Console\Alert;
use Simflex\Core\ConsoleBase;
use Simflex\Core\DB;

class ConsoleInstall extends ConsoleBase
{
    public function install()
    {
        Alert::text('Installing Simflex');

        // initialize db.
        DB::query(file_get_contents(SF_ROOT_PATH . '/database/init.sql'));
        if (DB::error()) {
            Alert::error('Failed to initialize database');
            Alert::text(DB::error());
            return;
        }

        Alert::success('Initialized database');

        $migrator = new DB\Migrator();
        $migrator->up();

        Alert::success('Install finished');
    }
}