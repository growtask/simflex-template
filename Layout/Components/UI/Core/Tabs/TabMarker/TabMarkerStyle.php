<?php

namespace App\Layout\Components\UI\Core\Tabs\TabMarker;

enum TabMarkerStyle: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Outline = 'outline';
    case Underline = 'underline';
    case Flat = 'flat';
}