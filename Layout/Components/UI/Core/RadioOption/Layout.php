<?php

namespace App\Layout\Components\UI\Core\RadioOption;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawRadio(
        string        $className = '',
        string        $text = '',
        string        $id = '',
        string        $value = '',
        RadioSize     $size = RadioSize::Small,
        RadioTheme    $theme = RadioTheme::Light,
        RadioPosition $position = RadioPosition::Left,
        array         $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'id',
                'value',
                'attributes'
            ) + [
                'size' => $size->value,
                'theme' => $theme->value,
                'position' => $position->value
            ]
        );
    }
}