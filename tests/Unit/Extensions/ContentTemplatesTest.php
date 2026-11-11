<?php

namespace Tests\Unit\Extensions;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Admin\Table\Repository;
use Simflex\Core\Extensions\Content\ContentTemplates;

/**
 * Шаблоны страниц и их поля объявлены в коде: provider/templates.php
 */
class ContentTemplatesTest extends Unit
{
    protected function _before(): void
    {
        ContentTemplates::reset();
    }

    public function testTemplatesAreReadFromProvider(): void
    {
        $this->assertSame('Главная', ContentTemplates::get('index')?->name);
        $this->assertSame('index.tpl', ContentTemplates::file('index'));
        // страница без шаблона или с неизвестным шаблоном
        $this->assertSame(ContentTemplates::DEFAULT_FILE, ContentTemplates::file(null));
        $this->assertSame(ContentTemplates::DEFAULT_FILE, ContentTemplates::file('removed'));
    }

    public function testTemplateFieldsAreFlatParamsInBlocks(): void
    {
        $rows = Repository::getTemplateParams('index');
        $this->assertSame([''], array_keys($rows));

        $byName = array_column($rows[''], null, 'name');
        $this->assertSame('SEO', $byName['seo_title_1']['group_name']);
        $this->assertSame('left', $byName['seo_title_1']['pos']);
        $this->assertSame(FieldString::class, $byName['seo_title_1']['class']);
        $this->assertSame('Мета', $byName['meta_title']['group_name']);
        $this->assertSame('right', $byName['meta_title']['pos']);
        $this->assertSame([], Repository::getTemplateParams('removed'));
    }
}
