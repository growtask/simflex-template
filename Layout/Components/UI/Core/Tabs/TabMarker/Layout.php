<?php

namespace App\Layout\Components\UI\Core\Tabs\TabMarker;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawTabMarker(
        string         $className = '',
        string         $text = '',
        string         $description = '',
        string         $link = '',
        string         $marker = '',
        string         $iconRight = '',
        string         $badge = '',
        TabMarkerStyle $style = TabMarkerStyle::Primary,
        TabMarkerSize  $size = TabMarkerSize::Medium,
        TabMarkerTheme $theme = TabMarkerTheme::Light,
        array          $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'description',
                'link',
                'marker',
                'iconRight',
                'badge',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}