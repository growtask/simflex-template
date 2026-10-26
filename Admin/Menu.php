<?php

namespace App\Admin;

use Simflex\Admin\MenuItem;

/**
 * Меню админки сайта: разделы ядра плюс добавленные здесь. Задаётся как adminMenuClass в config.php
 *
 * Новый раздел: static::add(new MenuItem('Блог', '/blog', handler: Handlers\Blog::class, icon: 'file'));
 * подпункт группы ядра: static::add(new MenuItem(...), parent: '/site');
 * перед другим пунктом: static::add(new MenuItem(...), before: '/content');
 * без раздела ядра: static::remove('/site/seo').
 */
class Menu extends \Simflex\Admin\Menu
{
    protected static function init(): void
    {
        parent::init();
    }
}
