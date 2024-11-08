<?php

/*
 * Fill the CLI command providers
 */

return [
    'migrate' => \Simflex\Core\DB\Cli\Migrator::class,
    'seed' => \Simflex\Core\DB\Cli\Seeder::class,
    'install' => \App\Extensions\Install\Installer::class,
    'layout' => \App\Extensions\Layout\ConsoleLayout::class,
];