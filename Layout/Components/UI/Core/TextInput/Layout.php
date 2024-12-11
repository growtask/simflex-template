<?php

namespace App\Layout\Components\UI\Core\TextInput;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawInput(
        string            $className = '',
        string            $placeholder = '',
        string            $labelText = '',
        TextInputLabelPos $labelPos = TextInputLabelPos::Hidden,
        TextInputStyle    $style = TextInputStyle::Filled,
        TextInputSize     $size = TextInputSize::Medium,
        TextInputTheme    $theme = TextInputTheme::Light,
        string            $icon = '',
        bool              $reset = true,
        string            $id = '',
        array             $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'placeholder',
                'labelText',
                'icon',
                'reset',
                'id',
                'attributes'
            ) + [
                'style' => $style->value,
                'labelPos' => $labelPos->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}