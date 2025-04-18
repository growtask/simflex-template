<?php

namespace App\Layout\Components\UI\Core\Tab;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawTab(
        string     $className = '',
        string     $text = '',
        string     $link = '',
        string     $iconLeft = '',
        string     $iconRight = '',
        string     $badge = '',
        TabStyle   $style = TabStyle::Primary,
        TabSize    $size = TabSize::Medium,
        TabTheme   $theme = TabTheme::Light,
        array      $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'link',
                'iconLeft',
                'iconRight',
                'badge',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}