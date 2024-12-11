<?php

namespace App\Layout\Components\UI\Core\BadgeStock;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawBadge(
        string          $className = '',
        string          $text = '',
        BadgeStockStyle $style = BadgeStockStyle::Many,
        BadgeStockSize  $size = BadgeStockSize::ExtraSmall,
        array           $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
            ]
        );
    }
}