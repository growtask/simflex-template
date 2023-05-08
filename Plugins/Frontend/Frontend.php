<?php

namespace App\Plugins\Frontend;

use Simflex\Admin\Page;

/**
 * PlugFrontEnd class
 *
 * Allow to output javascript and css from any programm point
 *
 * @author Evgeny Shilov <evgeny@internet-menu.ru>
 * @version 1.0
 */
class Frontend
{
    private static $css = array();
    private static $js = array();

    public static function tinymce()
    {
        Page::js('/plug/editor/tinymce/tinymce.js', 10);
    }

    public static function css($source)
    {
        self::$css[md5($source)] = $source;
    }

    public static function css_clear()
    {
        self::$css = array();
    }

    public static function css_content()
    {
        return join("\r\n", self::$css);
    }

    public static function js($source)
    {
        self::$js[md5($source)] = $source;
    }

    public static function js_clear()
    {
        self::$js = array();
    }

    public static function js_content()
    {
        return join("\r\n", self::$js);
    }

    public static function output()
    {
        if (count(self::$css)) {
            echo "\r\n" . '<style>';
            echo join("\r\n", self::$css);
            echo "\r\n" . '</style>';
        }

        if (count(self::$js)) {
            echo "\r\n" . '<script type="text/javascript">' . "\r\n";
            echo join("\r\n", self::$js);
            echo "\r\n" . '</script>' . "\r\n";
        }
    }

}