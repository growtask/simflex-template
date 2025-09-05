<?php

namespace App\Layout\Components\UI\Core\Selector\SelectorTab;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSelectorTab(
        string                 $className = '',
        string                 $text = '',
        bool                   $iconLeft = true,
        SelectorTabTheme       $theme = SelectorTabTheme::Light,
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'iconLeft'
            ) + [
                'theme' => $theme->value,
            ]
        );
    }
}