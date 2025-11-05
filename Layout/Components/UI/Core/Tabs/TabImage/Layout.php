<?php

namespace App\Layout\Components\UI\Core\Tabs\TabImage;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawTabImage(
        string         $className = '',
        string         $text = '',
        string         $description = '',
        string         $link = '',
        string         $image = '',
        string         $iconRight = '',
        string         $badge = '',
        TabImageStyle  $style = TabImageStyle::Primary,
        TabImageSize   $size = TabImageSize::Medium,
        TabImageTheme  $theme = TabImageTheme::Light,
        array          $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'description',
                'link',
                'image',
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