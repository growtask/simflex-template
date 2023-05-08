<?php


namespace App\Core\Console;


class Alert
{

    protected static $foregroundColors = [
        'black' => 30,
        'blue' => 34,
        'green' => 32,
        'cyan' => 36,
        'red' => 31,
        'purple' => 35,
        'yellow' => 33,
        'white' => 37,
    ];

    protected static $backgroundColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    protected static function getColoredString($string, $fgColor = null, $bgColor = null, $bold = false)
    {
        $result = "";
        if (isset(static::$foregroundColors[$fgColor])) {
            $result .= "\033[" . (int)$bold . ';' . static::$foregroundColors[$fgColor] . "m";
        }
        if (isset(static::$backgroundColors[$bgColor])) {
            $result .= "\033[" . static::$backgroundColors[$bgColor] . "m";
        }
        $result .= $string . "\033[0m";
        return $result;
    }


    public static function text($message)
    {
        echo $message . "\n";
    }

    public static function error($message)
    {
        echo static::getColoredString($message, 'red', null, true) . "\n";
    }

    public static function warning($message)
    {
        echo static::getColoredString($message, 'yellow', null, true) . "\n";
    }

    public static function success($message)
    {
        echo static::getColoredString($message, 'green', null, true) . "\n";
    }

    public static function result($success, $successMessage, $errorMessage)
    {
        if ($success) {
            static::success($successMessage);
        } else {
            static::error($errorMessage);
        }
    }


}