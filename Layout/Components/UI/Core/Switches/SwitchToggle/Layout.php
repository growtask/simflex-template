<?php

namespace App\Layout\Components\UI\Core\Switches\SwitchToggle;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSwitchToggle(
        string                 $className = '',
        string                 $text = '',
        bool                   $icon = true,
        SwitchToggleSize       $size = SwitchToggleSize::Small,
        SwitchToggleSizeToggle $toggleSize = SwitchToggleSizeToggle::Medium,
        SwitchToggleTheme      $theme = SwitchToggleTheme::Light,
        SwitchTogglePosition   $togglePosition = SwitchTogglePosition::Left,
        array                  $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'icon',
                'attributes'
            ) + [
                'toggleSize' => $toggleSize->value,
                'size' => $size->value,
                'theme' => $theme->value,
                'togglePosition' => $togglePosition->value
            ]
        );
    }
}