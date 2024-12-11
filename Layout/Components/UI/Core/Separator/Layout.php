<?php

namespace App\Layout\Components\UI\Core\Separator;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSeparator(
        string               $className = '',
        SeparatorOrientation $orientation = SeparatorOrientation::Horizontal,
        SeparatorTheme       $theme = SeparatorTheme::Light,
        array                $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'attributes'
            ) + [
                'orientation' => $orientation->value,
                'theme' => $theme->value,
            ]
        );
    }
}