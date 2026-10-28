<?php

namespace Tests\Unit\Layout;

use Simflex\Layout\ConsoleLayout;
use Simflex\Layout\LayoutBase;
use Simflex\Layout\LayoutManager;
use Codeception\Test\Unit;
use Simflex\Admin\Fields\FieldText;

/**
 * Макеты: части страницы со своим шаблоном и объявленными полями (./sf layout/generate)
 */
class LayoutTest extends Unit
{
    private bool $shouldGenerateFields;
    private string $dir = '';

    protected function _before(): void
    {
        $this->shouldGenerateFields = LayoutManager::$shouldGenerateFields;
        LayoutManager::$shouldGenerateFields = true;
        LayoutManager::$generatedFields = [];
        $this->dir = sys_get_temp_dir() . '/sf-layout-' . uniqid();
        mkdir($this->dir);
        LayoutTestPlain::$dir = LayoutTestWithFields::$dir = $this->dir;
    }

    protected function _after(): void
    {
        LayoutManager::$shouldGenerateFields = $this->shouldGenerateFields;
        LayoutManager::$generatedFields = [];
        exec('rm -rf ' . escapeshellarg($this->dir));
    }

    public function testLayoutWithoutFieldsIsDrawnWithoutWarnings(): void
    {
        file_put_contents($this->dir . '/layout.tpl', 'layout:<?= $data["x"] ?>');
        set_error_handler(function (int $no, string $str) {
            throw new \ErrorException($str, 0, $no);
        });
        try {
            $this->assertSame('layout:ok', LayoutTestPlain::draw(['x' => 'ok']));
        } finally {
            restore_error_handler();
        }
        $this->assertSame([], LayoutManager::$generatedFields);
    }

    public function testLayoutFieldsAreGeneratedAsCode(): void
    {
        file_put_contents($this->dir . '/layout.tpl', 'hero');
        $this->assertSame('hero', LayoutTestWithFields::draw(['__prefix' => 'top']));

        $this->assertSame(['Hero'], array_keys(LayoutManager::$generatedFields));
        $code = ConsoleLayout::code(LayoutManager::$generatedFields);

        $this->assertStringContainsString("new ContentTemplateBlock('Hero', [", $code);
        $this->assertStringContainsString(
            "new FieldDefinition(name: 'top_title', class: \\Simflex\\Admin\\Fields\\FieldString::class, label: 'Заголовок', "
            . "defaultValue: 'Привет'),",
            $code
        );
        $this->assertStringContainsString(', editorFull: true)', $code);
    }

    /**
     * Команда рисует страницу с компонентом, созданным так же, как это делает роутер (с зависимостями)
     */
    public function testGenerateCommandDrawsPage(): void
    {
        $output = (string)shell_exec('cd ' . escapeshellarg(SF_ROOT_PATH) . ' && php sf layout/generate / 2>&1');

        $this->assertStringNotContainsString('error', $output);
        $this->assertStringContainsString('The layouts of the page declare no fields', $output);
    }
}

class LayoutTestPlain extends LayoutBase
{
    public static string $dir = '';

    protected static function layoutRootDir(): string
    {
        return static::$dir;
    }
}

class LayoutTestWithFields extends LayoutTestPlain
{
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
