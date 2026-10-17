<?php

namespace Tests\Unit;

use App\Extensions\Layout\ConsoleLayout;
use App\Layout\LayoutBase;
use App\Layout\LayoutManager;
use Codeception\Test\Unit;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Repository;
use Simflex\Extensions\Content\ContentTemplates;

/**
 * Page templates and their fields are declared in code: provider/templates.php
 */
class ContentTemplatesTest extends Unit
{
    protected function _before(): void
    {
        $root = dirname(__DIR__, 2);
        defined('SF_CORE_ROOT_PATH') || require_once $root . '/vendor/growtask/simflex/src/constants.php';
        defined('SF_ROOT_PATH') || require_once $root . '/constants.php';
        ContentTemplates::reset();
    }

    public function testTemplatesAreReadFromProvider(): void
    {
        $this->assertSame('Главная', ContentTemplates::get('index')?->name);
        $this->assertSame('index.tpl', ContentTemplates::file('index'));
        // a page without a template or with an unknown one
        $this->assertSame(ContentTemplates::DEFAULT_FILE, ContentTemplates::file(null));
        $this->assertSame(ContentTemplates::DEFAULT_FILE, ContentTemplates::file('removed'));
    }

    public function testTemplateFieldsAreFlatParamsInBlocks(): void
    {
        $rows = Repository::templateParams('index');
        $this->assertSame([''], array_keys($rows));

        $byName = array_column($rows[''], null, 'name');
        $this->assertSame('SEO', $byName['seo_title_1']['group_name']);
        $this->assertSame('left', $byName['seo_title_1']['pos']);
        $this->assertSame(FieldString::class, $byName['seo_title_1']['class']);
        $this->assertSame('Мета', $byName['meta_title']['group_name']);
        $this->assertSame('right', $byName['meta_title']['pos']);
        $this->assertSame([], Repository::templateParams('removed'));
    }

    public function testLayoutFieldsAreGeneratedAsCode(): void
    {
        $prev = LayoutManager::$shouldGenerateFields;
        LayoutManager::$shouldGenerateFields = true;
        LayoutManager::$generatedFields = [];
        $dir = sys_get_temp_dir() . '/sf-layout-' . uniqid();
        mkdir($dir);
        file_put_contents($dir . '/layout.tpl', 'hero');
        ContentTemplatesTestLayout::$dir = $dir;
        try {
            $this->assertSame('hero', ContentTemplatesTestLayout::draw(['__prefix' => 'top']));
        } finally {
            LayoutManager::$shouldGenerateFields = $prev;
            unlink($dir . '/layout.tpl');
            rmdir($dir);
        }

        $this->assertSame(['Hero'], array_keys(LayoutManager::$generatedFields));
        $code = ConsoleLayout::code(LayoutManager::$generatedFields);
        LayoutManager::$generatedFields = [];

        $this->assertStringContainsString("new ContentTemplateBlock('Hero', [", $code);
        $this->assertStringContainsString(
            "new FieldDefinition(name: 'top_title', class: \\Simflex\\Admin\\Fields\\FieldString::class, label: 'Заголовок', "
            . "params: ['main' => new FieldParams(defaultValue: 'Привет')]),",
            $code
        );
        $this->assertStringContainsString(
            "params: ['main' => new FieldParams(editorFull: true)]",
            $code
        );
    }
}

class ContentTemplatesTestLayout extends LayoutBase
{
    public static string $dir = '';

    protected static function layoutRootDir(): string
    {
        return static::$dir;
    }

    protected static function getFieldSchema(): array
    {
        return [
            'group' => 'Hero',
            'prefix' => 'hero',
            'items' => [
                ['id' => 'title', 'name' => 'Заголовок', 'type' => 'string', 'default' => 'Привет'],
                ['id' => 'text', 'name' => 'Текст', 'type' => FieldText::class, 'params' => ['editor_full' => true]],
            ],
        ];
    }
}
