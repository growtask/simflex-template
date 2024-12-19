<?php

namespace App\Layout\Components\UI\Core\Tab;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawTab(
        string     $className = '',
        string     $text = '',
        string     $link = '',
        string     $icon = '',
        string     $badge = '',
        TabChevron $chevron = TabChevron::None,
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
                'icon',
                'badge',
                'attributes'
            ) + [
                'chevron' => $chevron->value,
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}