<?php

namespace App\Extensions\Breadcrumbs;

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
