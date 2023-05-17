<?php

namespace App\Layout\Cards\Products1;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static $extraAssets = [];

    protected static function getFieldSchema(): array
    {
        return [
            'items' => [
                [
                    'id' => 'title',
                    'name' => 'Заголовок',
                    'type' => 'string',
                    'default' => '',
                    'params' => ''
                ]
            ],
            'group' => 'Параметры',
            'prefix' => 'prods'
        ];
    }
}