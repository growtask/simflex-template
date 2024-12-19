<?php

namespace App\Layout\Components\UI\Core\DropdownTab;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawDropdownTab(
        string               $className = '',
        string               $text = '',
        string               $link = '',
        string               $icon = '',
        string               $badge = '',
        DropdownTabMenuTheme $menuTheme = DropdownTabMenuTheme::Light,
        DropdownTabChevron   $chevron = DropdownTabChevron::None,
        DropdownTabStyle     $style = DropdownTabStyle::Primary,
        DropdownTabSize      $size = DropdownTabSize::Medium,
        DropdownTabTheme     $theme = DropdownTabTheme::Light,
        array                $options = [],
        array                $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'link',
                'icon',
                'badge',
                'options',
                'attributes'
            ) + [
                'menuTheme' => $menuTheme->value,
                'chevron' => $chevron->value,
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}