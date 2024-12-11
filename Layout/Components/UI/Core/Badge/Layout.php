<?php

namespace App\Layout\Components\UI\Core\Badge;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawBadge(
        string     $className = '',
        string     $text = '',
        BadgeStyle $style = BadgeStyle::Primary,
        BadgeSize  $size = BadgeSize::ExtraSmall,
        string     $icon = '',
        bool       $dot = false,
        array      $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'icon',
                'dot',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
            ]
        );
    }
}