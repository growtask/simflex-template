<?php
namespace App\Layout\Components\UI\Core\Badges\BadgeMain;

enum BadgeMainStyle: string
{
    case Error = 'error';
    case Alert = 'alert';
    case Success = 'success';
    case Primary = 'primary';
    case PrimaryLight = 'primary-light';
    case Secondary = 'secondary';
    case Accent = 'accent';
    case AccentLight = 'accent-light';
    case Gray = 'gray';
    case Dark = 'dark';
    case White = 'white';
    case Gradient = 'gradient';
    case GradientLight = 'gradient-light';
    case Sale = 'sale';
    case New = 'new';
    case NoStock = 'no-stock';
}