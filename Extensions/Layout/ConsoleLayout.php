<?php

namespace App\Extensions\Layout;

use App\Extensions\Content\Content;
use App\Layout\LayoutManager;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\Help;
use Simflex\Core\ConsoleBase;
use Simflex\Core\Container;
use Simflex\Core\DI\Injector;
use Simflex\Core\Html\HtmlRequest;
use Simflex\Core\Log;

class ConsoleLayout extends ConsoleBase
{
    /**
     * Draws the page and prints the fields declared by its Layout components (getFieldSchema()) as the code
     * of ContentTemplateBlock-s: put it into the params of the page's template in provider/templates.php
     */
    #[Command('Generate the code of the layout fields of a page template')]
    public function generate(#[Help('Page path')] string $path): void
    {
        // set bogus data.
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Container::set('request', new HtmlRequest());

        LayoutManager::$shouldGenerateFields = true;
        LayoutManager::$generatedFields = [];

        try {
            $content = new Content(...Injector::resolveClass(Content::class));
            $content->execute();
        } catch (\Throwable $ex) {
            Log::error($ex->getMessage());
        }

        LayoutManager::$shouldGenerateFields = false;

        if (!LayoutManager::$generatedFields) {
            Log::notice('The layouts of the page declare no fields');
            return;
        }

        echo static::code(LayoutManager::$generatedFields), "\n";
    }

    /**
     * @param array<string, list<array>> $blocks block label => fields, see LayoutManager::$generatedFields
     */
    public static function code(array $blocks): string
    {
        $code = '';
        foreach ($blocks as $label => $fields) {
            $code .= 'new ContentTemplateBlock(' . var_export((string)$label, true) . ", [\n";
            foreach ($fields as $field) {
                $args = [
                    'name: ' . var_export($field['name'], true),
                    'class: \\' . ltrim($field['class'], '\\') . '::class',
                    'label: ' . var_export($field['label'], true),
                ];
                $params = [];
                if ($field['default'] !== '') {
                    $params[] = 'defaultValue: ' . var_export($field['default'], true);
                }
                foreach ($field['params'] as $key => $value) {
                    $key = lcfirst(str_replace('_', '', ucwords((string)$key, '_')));
                    $params[] = $key . ': ' . var_export($value, true);
                }
                if ($params) {
                    $args[] = "params: ['main' => new FieldParams(" . implode(', ', $params) . ')]';
                }
                $code .= '    new FieldDefinition(' . implode(', ', $args) . "),\n";
            }
            $code .= "]),\n";
        }

        return $code;
    }
}
