<?php

namespace App\Core;

use Simflex\Core\PrivilegeParams;

/**
 * Privileges of the site: the core ones (DEV, ADMIN) plus the ones below. Set as privilegeClass in config.php
 */
class Privilege extends \Simflex\Core\Privilege
{
    public const MANAGER = 'manager';

    protected static function init(): void
    {
        parent::init();
        static::define(self::MANAGER, new PrivilegeParams('Привилегия менеджера'));
    }
}
