<?php

/*
 * Fill the CLI command providers
 */

return [
    'migrate' => \Simflex\Core\DB\Migrator::class,
    'install' => \App\Extensions\Install\Installer::class,
    'layout' => \App\Extensions\Layout\ConsoleLayout::class,
];