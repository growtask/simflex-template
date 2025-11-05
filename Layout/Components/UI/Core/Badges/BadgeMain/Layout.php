<?php

namespace App\Layout\Components\UI\Core\Badges\BadgeMain;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawBadgeMain(
        string          $className = '',
        string          $text = '',
        BadgeMainStyle  $style = BadgeMainStyle::Primary,
        BadgeMainSize   $size = BadgeMainSize::ExtraSmall,
        string          $icon = '',
        bool            $dot = false,
        array           $attributes = []
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