<?php

/*
 * Провайдеры CLI-команд сайта: имя => класс-наследник ConsoleBase.
 * Команды ядра (migrate, layout, cron) и расширений подключаются автоматически; провайдер сайта с тем же именем главнее
 */

return [
    'install' => \App\Extensions\Install\Installer::class,
];
