<?php

namespace App\Core;

use Simflex\Core\System\PrivilegeParams;

/**
 * Привилегии сайта: привилегии ядра (DEV, ADMIN) плюс объявленные ниже. Задаётся как privilegeClass в config.php
 */
class Privilege extends \Simflex\Core\System\Privilege
{
    public const MANAGER = 'manager';

    protected static function init(): void
    {
        parent::init();
        static::define(self::MANAGER, new PrivilegeParams('Привилегия менеджера'));
    }
}
