<?php

namespace App\Layout\Components\UI\Core\Selector\SelectorTab;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSelectorTab(
        string                 $className = '',
        string                 $text = '',
        bool                   $isActive = false,
        bool                   $iconLeft = true,
        SelectorTabTheme       $theme = SelectorTabTheme::Light,
        array                  $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'isActive',
                'iconLeft',
                'attributes'
            ) + [
                'theme' => $theme->value,
            ]
        );
    }
}