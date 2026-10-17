<?php

namespace Tests\Unit\Extensions;

use Codeception\Test\Unit;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Structure\Repository;
use Simflex\Extensions\Content\ContentTemplates;

/**
 * Page templates and their fields are declared in code: provider/templates.php
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
}
