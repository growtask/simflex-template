<?php

namespace App\Layout\Components\UI\Core\Tab;

enum TabChevron: string
{
    case None = '';
    case Left = 'left';
    case Right = 'right';
    case Up = 'up';
    case Down = 'down';
}