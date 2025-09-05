<?php

namespace App\Layout\Components\UI\Core\Switches\SwitchRadio;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawSwitchRadio(
        string          $className = '',
        string          $text = '',
        string          $id = '',
        string          $value = '',
        RadioSize       $size = RadioSize::Small,
        RadioSizeRadio  $radioSize = RadioSizeRadio::Small,
        RadioTheme      $theme = RadioTheme::Light,
        RadioPosition   $radioPosition = RadioPosition::Left,
        array           $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'text',
                'id',
                'value',
                'attributes'
            ) + [
                'radioSize' => $radioSize->value,
                'size' => $size->value,
                'theme' => $theme->value,
                'radioPosition' => $radioPosition->value
            ]
        );
    }
}