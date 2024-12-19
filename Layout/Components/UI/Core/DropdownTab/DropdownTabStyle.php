<?php

namespace App\Layout\Components\UI\Core\DropdownTab;

enum DropdownTabStyle: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Outline = 'outline';
    case Underline = 'underline';
    case Flat = 'flat';
}