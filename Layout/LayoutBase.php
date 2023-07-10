<?php

namespace App\Layout;

use Simflex\Core\Buffer;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Path;
use Simflex\Extensions\Content\Model\ModelContent;

abstract class LayoutBase
{
    public const FIELD_STRING = 'string';
    public const FIELD_TEXT = 'text';
    public const FIELD_TABLE = 'table';
    public const FIELD_IMAGE = 'image';
    public const FIELD_FILE = 'file';

    public static $extraAssets = [];

    protected static $inputData = [];

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
        // generate fields if requested.
        if (LayoutManager::$shouldGenerateFields && !$data['__no_gen']) {
            self::$inputData = $data;
            self::generateFields();
        }

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

    protected static function getFieldSchema(): array
    {
        // stub.

        // items structure:
        // - name: name
        // - id: name id
        // - type: field type
        // - default: field default value
        // - params: field additional parameters
        return [
            'items' => [],
            'group' => '',
            'prefix' => '',
        ];
    }

    protected static function generateFields()
    {
        $schema = static::getFieldSchema();
        if (self::$inputData['__group'] ?? false) {
            $schema['group'] = self::$inputData['__group'];
        }

        if (self::$inputData['__prefix'] ?? false) {
            $schema['prefix'] = self::$inputData['__prefix'];
        }

        $fields = $schema['items'];

        // get current page.
        $page = ModelContent::findOne(['path' => Container::getRequest()->getPath()]);

        // delete the entire group.
        if ($schema['group']) {
            DB::query(
                'DELETE FROM content_template_param WHERE group_name = ? AND template_id = ?',
                [$schema['group'], $page->template_id]
            );
        }

        // create new fields.
        // set correct order - by counter on that particular template.
        // set the default name from schema.

        if (!isset(LayoutManager::$templateOrdering[$page->template_id])) {
            LayoutManager::$templateOrdering[$page->template_id] = 0;
        }

        foreach ($fields as $field) {
            DB::query(
                'INSERT INTO content_template_param (template_id, param_pid, position, field_id, name, label, npp, params, default_value, group_name) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $page->template_id,
                    'left', // TODO: allow to change from schema?
                    self::findFieldId($field['type']),
                    self::mangleName($schema, $field),
                    $field['name'],
                    LayoutManager::$templateOrdering[$page->template_id] += 10, // give up some space, just in case.
                    serialize(['main' => $field['params'] ?? []]),
                    $field['default'] ?? '',
                    $field['group'] ?? $schema['group']
                ]
            );

            if (DB::error()) {
                throw new \Exception(DB::error());
            }
        }
    }

    protected static function mangleName(array $schema, array $field): string
    {
        return $schema['prefix'] . '_' . $field['id'];
    }

    protected static function findFieldId(string $type): int
    {
        // TODO: optimize this

        $q = DB::query('SELECT field_id, class FROM struct_field');
        while ($r = DB::fetch($q)) {
            if (strpos(strtolower($r['class']), $type) !== false) {
                return $r['field_id'];
            }
        }

        return 0;
    }
}