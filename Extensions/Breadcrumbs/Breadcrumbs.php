<?php

namespace App\Extensions\Breadcrumbs;

use Simflex\Core\Core;
use Simflex\Core\ModuleBase;

class Breadcrumbs extends \Simflex\Extensions\Breadcrumbs\Breadcrumbs
{
    public static function getBeforeLast(): array
    {
        $keys = array_keys(self::$arr);
        if (count($keys) <= 1) {
            return [];
        }

        return self::$arr[$keys[1]] ?? [];
    }
}
