<?php

namespace App\Layout\Components\UI\Core\DropdownTab;

enum DropdownTabChevron: string
{
    case None = '';
    case Left = 'left';
    case Right = 'right';
    case Up = 'up';
    case Down = 'down';
}