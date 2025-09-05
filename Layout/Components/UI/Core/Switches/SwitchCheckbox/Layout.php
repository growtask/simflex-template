<?php

namespace App\Layout\Components\UI\Core\Switches\SwitchCheckbox;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSwitchCheckbox(
        string                 $className = '',
        string                 $text = '',
        SwitchCheckboxStyle    $style = SwitchCheckboxStyle::Solid,
        SwitchCheckboxSizeIcon $iconSize = SwitchCheckboxSizeIcon::Small,
        SwitchCheckboxSize     $size = SwitchCheckboxSize::Small,
        SwitchCheckboxTheme    $theme = SwitchCheckboxTheme::Light,
        SwitchCheckboxPosition $iconPosition = SwitchCheckboxPosition::Left,
        array                  $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'attributes'
            ) + [
                'style' => $style->value,
                'iconSize' => $iconSize->value,
                'size' => $size->value,
                'theme' => $theme->value,
                'iconPosition' => $iconPosition->value
            ]
        );
    }
}