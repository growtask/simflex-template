<?php

namespace App\Layout\Components\UI\Core\ToggleSwitch;


use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawToggle(
        string         $className = '',
        string         $text = '',
        bool           $icon = true,
        ToggleSize     $size = ToggleSize::Medium,
        ToggleTheme    $theme = ToggleTheme::Light,
        TogglePosition $position = TogglePosition::Left,
        array          $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'icon',
                'attributes'
            ) + [
                'size' => $size->value,
                'theme' => $theme->value,
                'position' => $position->value
            ]
        );
    }
}