<?php

namespace App\Layout\Components\UI\Core\Tabs\TabHeadling;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawTabHeadling(
        string         $className = '',
        string         $text = '',
        string         $link = '',
        string         $badge = '',
        array          $attributes = []
    ): void
    {
        static::draw(compact(
            'className',
            'text',
            'link',
            'badge',
            'attributes'
        ));
    }
}