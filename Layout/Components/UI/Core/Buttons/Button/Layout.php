<?php

namespace App\Layout\Components\UI\Core\Buttons\Button;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawButton(
        string        $className = '',
        string        $text = '',
        string        $link = '',
        ButtonStyle   $style = ButtonStyle::Primary,
        ButtonSize    $size = ButtonSize::Medium,
        ButtonTheme   $theme = ButtonTheme::Light,
        string        $icon = '',
        ButtonIconPos $iconPos = ButtonIconPos::Left,
        array         $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'link',
                'icon',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
                'iconPos' => $iconPos->value
            ]);
    }
}