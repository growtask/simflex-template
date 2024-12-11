<?php

namespace App\Layout\Components\UI\Core\Accordion;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static function drawAccordion(
        string         $className = '',
        string         $title = '',
        string         $subtitle = '',
        string         $comment = '',
        string         $badge = '',
        string         $icon = '',
        string         $description = '',
        AccordionStyle $style = AccordionStyle::Filled,
        AccordionSize  $size = AccordionSize::Small,
        AccordionTheme $theme = AccordionTheme::Light,
        array          $attributes = []
    ): void
    {
        static::draw(compact(
                'className',
                'title',
                'subtitle',

                'comment',
                'badge',
                'icon',
                'description',
                'attributes'
            ) + [
                'style' => $style->value,
                'size' => $size->value,
                'theme' => $theme->value,
            ]
        );
    }
}