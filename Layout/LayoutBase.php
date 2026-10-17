<?php

namespace App\Layout;

use Simflex\Core\Buffer;
use Simflex\Core\Container;
use Simflex\Core\Path;
use Simflex\Core\View;

abstract class LayoutBase
{
    public const FIELD_STRING = 'string';
    public const FIELD_TEXT = 'text';
    public const FIELD_TABLE = 'table';
    public const FIELD_IMAGE = 'image';
    public const FIELD_FILE = 'file';

    public static array $extraAssets = [];

    protected static array $inputData = [];

    public static function initAssets(): void
    {
        $page = Container::getPage();
        foreach (static::$extraAssets as $asset) {
            if (is_array($asset)) {
                $page::addAsset($asset['file'], $asset['idx'] ?? 100, $asset['type'] ?? null);
                continue;
            }
            $page::addAsset($asset);
        }

        if (is_file(static::layoutRootDir() . '/script.js')) {
            LayoutManager::useScript(static::layoutRootHref() . '/script.js');
            $page::js(static::layoutRootHref() . '/script.js');
        }

        LayoutManager::useStyle(static::layoutRootDir() . '/style.css');
        LayoutManager::useStyle(static::layoutRootDir() . '/style.scss');
    }

    /**
     * Returns the component HTML
     */
    public static function draw(array $data = []): string
    {
        // generate fields if requested.
        if (LayoutManager::$shouldGenerateFields && empty($data['__no_gen'])) {
            self::$inputData = $data;
            self::generateFields();
        }

        LayoutManager::useLayout(static::class);
        return View::render(static::layoutRootDir() . '/layout.tpl', ['data' => $data], static::class);
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

    /**
     * Collects the fields of the layout's schema into LayoutManager::$generatedFields: the page template's fields
     * are declared in code (provider/templates.php), ./sf layout/generate prints them as code to put there
     */
    protected static function generateFields(): void
    {
        $schema = static::getFieldSchema();
        if (self::$inputData['__group'] ?? false) {
            $schema['group'] = self::$inputData['__group'];
        }

        if (self::$inputData['__prefix'] ?? false) {
            $schema['prefix'] = self::$inputData['__prefix'];
        }

        foreach ($schema['items'] as $field) {
            $group = (string)($field['group'] ?? $schema['group']);
            LayoutManager::$generatedFields[$group][] = [
                'name' => self::mangleName($schema, $field),
                'label' => (string)$field['name'],
                'class' => self::findFieldType($field['type']),
                'default' => (string)($field['default'] ?? ''),
                'params' => $field['params'] ?? [],
            ];
        }
    }

    protected static function mangleName(array $schema, array $field): string
    {
        return $schema['prefix'] . '_' . $field['id'];
    }

    protected static function findFieldType(string $type): string
    {
        if (str_contains($type, '\\')) {
            return ltrim($type, '\\');
        }

        return 'Simflex\\Admin\\Fields\\Field' . ucfirst($type);
    }
}
