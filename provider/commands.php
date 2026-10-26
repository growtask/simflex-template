<?php

/*
 * Провайдеры CLI-команд
 */

return [
    'migrate' => \Simflex\Core\DB\Cli\Migrator::class,
    'install' => \App\Extensions\Install\Installer::class,
    'layout' => \App\Extensions\Layout\ConsoleLayout::class,
];
