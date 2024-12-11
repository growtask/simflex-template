<?php

namespace App\Layout\Components\UI\Core\Tag;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawTag(
        string      $className = '',
        string      $text = '',
        TagStyle    $style = TagStyle::Primary,
        TagSize  $size = TagSize::Small,
        TagTheme $theme = TagTheme::Light,
        array       $attributes = []
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
            ]
        );
    }
}