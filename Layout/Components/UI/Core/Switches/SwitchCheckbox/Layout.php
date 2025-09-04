<?php

namespace App\Layout\Components\UI\Core\Switches\SwitchCheckbox;

use App\Layout\Components\UI\Core\Switches\SwitchCheckbox\SwitchCheckboxPosition;
use App\Layout\Components\UI\Core\Switches\SwitchCheckbox\SwitchCheckboxSize;
use App\Layout\Components\UI\Core\Switches\SwitchCheckbox\SwitchCheckboxStyle;
use App\Layout\Components\UI\Core\Switches\SwitchCheckbox\SwitchCheckboxTheme;
use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSwitchCheckbox(
        string                 $className = '',
        string                 $text = '',
        SwitchCheckboxStyle    $style = SwitchCheckboxStyle::Solid,
        SwitchCheckboxSize     $size = SwitchCheckboxSize::Small,
        SwitchCheckboxTheme    $theme = SwitchCheckboxTheme::Light,
        SwitchCheckboxPosition $position = SwitchCheckboxPosition::Left,
        array                  $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
                'position' => $position->value
            ]
        );
    }
}