<?php

namespace App\Layout\Components\Common\Core\Main\MainImg;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawImg(
        string         $className = '',
        string         $src = '/assets/images/no-image.webp',
        string         $alt = '',
        array          $attributes = []
    ): void
    {
        static::draw(compact(
            'className',
            'src',
            'alt',
            'attributes'
        ));
    }
}