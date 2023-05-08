<?php

namespace App\Plugins\Jquery;


use Simflex\Core\Container;

class Jquery
{

    public static function webPath()
    {
        return str_replace(SF_ROOT_PATH, '', __DIR__);
    }

    public static function core()
    {
        Container::getPage()::js('//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', 0);
//        SFPage::js('http://code.jquery.com/jquery-migrate-1.4.0.js', 0);
    }

    public static function fancybox()
    {
        Container::getPage()::js(static::webPath() . '/plugins/fancybox/jquery.fancybox.pack.js', 10);
        Container::getPage()::css(static::webPath() . '/plugins/fancybox/jquery.fancybox.css', 10);
        Container::getPage()::js(static::webPath() . '/plugins/fancybox/jquery.fancybox.simplex.js', 10);
//        PlugFrontEnd::js("$(function(){\$('.fancybox, .lightview').fancybox()})");
//        PlugFrontEnd::js("$(function(){\$('.fancybox, .lightview').fancybox({helpers: {overlay: {locked: false}}})})");
    }

    public static function owlCarousel()
    {
        Container::getPage()::css(static::webPath() . '/plugins/owl-carousel/owl.carousel.css');
        Container::getPage()::js(static::webPath() . '/plugins/owl-carousel/owl.carousel.min.js');
    }

    public static function maskedInput()
    {
        Container::getPage()::js(static::webPath() . '/plugins/maskedinput/jquery.maskedinput.js', 10);
        Container::getPage()::js(static::webPath() . '/plugins/maskedinput/maskedinput.init.js', 10);
    }

    public static function numericInput()
    {
        Container::getPage()::js(static::webPath() . '/plugins/numericinput/numericinput.js', 10);
    }

}
