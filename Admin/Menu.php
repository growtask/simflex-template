<?php

namespace App\Admin;

use Simflex\Admin\MenuItem;

/**
 * Admin panel menu of the site: the core sections plus the ones added here. Set as adminMenuClass in config.php
 *
 * A new section: static::add(new MenuItem('Блог', '/blog', handler: Handlers\Blog::class, icon: 'file'));
 * a subitem of a core group: static::add(new MenuItem(...), parent: '/site');
 * before another item: static::add(new MenuItem(...), before: '/content');
 * without a core section: static::remove('/site/seo').
 */
class Menu extends \Simflex\Admin\Menu
{
    protected static function init(): void
    {
        parent::init();
    }
}
