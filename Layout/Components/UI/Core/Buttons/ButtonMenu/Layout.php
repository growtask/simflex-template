<?php

namespace App\Layout\Components\UI\Core\Buttons\ButtonMenu;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawButtonMenu(
        string          $className = '',
        string          $text = '',
        string          $link = '',
        string          $icon = 'true',
        string          $badge = '',
        ButtonMenuStyle $style = ButtonMenuStyle::Flat,
        ButtonMenuSize  $size = ButtonMenuSize::Medium,
        ButtonMenuTheme $theme = ButtonMenuTheme::Light,
        array           $attributes = []
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
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}