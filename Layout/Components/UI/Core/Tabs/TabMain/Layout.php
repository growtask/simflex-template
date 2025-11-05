<?php

namespace App\Layout\Components\UI\Core\Tabs\TabMain;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawTabMain(
        string        $className = '',
        string        $text = '',
        string        $link = '',
        string        $iconLeft = '',
        string        $iconRight = '',
        string        $badge = '',
        TabMainStyle  $style = TabMainStyle::Primary,
        TabMainSize   $size = TabMainSize::Medium,
        TabMainTheme  $theme = TabMainTheme::Light,
        array         $attributes = []
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