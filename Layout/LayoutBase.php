<?php

namespace App\Layout;

use Simflex\Core\Buffer;
use Simflex\Core\Container;
use Simflex\Core\Path;

abstract class LayoutBase
{

    public static $extraAssets = [];

    public static function initAssets()
    {
        foreach (static::$extraAssets as $asset) {
            if (is_array($asset)) {
                Container::getPage()::addAsset($asset['file'], $asset['idx'] ?? 100, $asset['type'] ?? null);
                continue;
            }
            Container::getPage()::addAsset($asset);
        }

        if (is_file(static::layoutRootDir() . '/script.js')) {
            Container::getPage()::js(static::layoutRootHref() . '/script.js');
        }

        LayoutManager::useStyle(static::layoutRootDir() . '/style.css');
        LayoutManager::useStyle(static::layoutRootDir() . '/style.scss');
    }

    public static function draw(array $data = [])
    {
        LayoutManager::useLayout(static::class);
        include static::layoutRootDir() . '/layout.tpl';
    }

    protected static function layoutRootDir(): string
    {
        return Buffer::getOrSet('layoutRootDir-' . static::class, function () {
            $ref = new \ReflectionClass(static::class);
            return dirname($ref->getFileName());
        });
    }

    protected static function layoutRootHref(): string
    {
        return Path::dirToHref(static::layoutRootDir());
    }
}