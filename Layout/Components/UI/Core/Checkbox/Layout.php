<?php

namespace App\Layout\Components\UI\Core\Checkbox;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawCheckbox(
        string           $className = '',
        string           $text = '',
        bool             $policy = false,
        CheckboxStyle    $style = CheckboxStyle::Solid,
        CheckboxSize     $size = CheckboxSize::Small,
        CheckboxTheme    $theme = CheckboxTheme::Light,
        CheckboxPosition $position = CheckboxPosition::Left,
        array            $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'policy',
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