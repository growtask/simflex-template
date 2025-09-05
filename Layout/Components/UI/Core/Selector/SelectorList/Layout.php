<?php

namespace App\Layout\Components\UI\Core\Selector\SelectorList;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSelectorList(
        string                 $className = '',
        array                  $tabsArr = [
            [ 'isActive' => true, 'text' => 'Tab' ],
            [ 'text' => 'Tab' ]
        ],
        SelectorListTheme      $theme = SelectorListTheme::Light,
    ): void
    {
        static::draw(compact(
                'className',
                'tabsArr'
            ) + [ 'theme' => $theme->value ]
        );
    }
}