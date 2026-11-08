<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Fields\FieldEnum;
use Simflex\Core\Admin\Fields\FieldFile;
use Simflex\Core\Admin\Fields\FieldMultiKey;
use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Admin\Fields\FieldVirtual;

/**
 * Значения полей в списке и карточке записи экранируются (главная колонка name и обычные ячейки)
 */
class ListCellEscapeTest extends Unit
{
    private const PAYLOAD = '<i>x</i><span onmouseover="a()">y</span>';

    public function testMainColumnIsEscaped(): void
    {
        $field = new FieldString(['name' => 'name', 'label' => 'Name', 'table' => 't']);
        $field->tablePk = 'id';
        $field->pkValue = 5;

        $html = $field->show(['id' => 5, 'name' => self::PAYLOAD]);

        $this->assertStringNotContainsString('<i>', $html);
        $this->assertStringNotContainsString('<span', $html);
        $this->assertStringContainsString('&lt;i&gt;x&lt;/i&gt;', $html);
        $this->assertStringContainsString('href="?action=form&id=5"', $html);
    }

    public function testOrdinaryCellAndDetailAreEscaped(): void
    {
        $field = new FieldString(['name' => 'title', 'label' => 'Title', 'table' => 't']);
        $field->tablePk = 'id';

        $cell = $field->show(['id' => 1, 'title' => self::PAYLOAD]);
        $this->assertStringNotContainsString('<i>', $cell);
        $this->assertStringContainsString('&lt;span onmouseover=', $cell);

        $detail = $field->showDetail(['id' => 1, 'title' => self::PAYLOAD]);
        $this->assertStringNotContainsString('<', $detail);
    }

    public function testFileNameIsEscapedAndEncodedInHref(): void
    {
        $field = new FieldFile(['name' => 'doc', 'label' => 'Doc', 'table' => 't', 'params' => ['main' => ['path' => 'files']]]);
        $name = '"><i>x</i> a&b.pdf';

        $html = $field->show(['doc' => $name]);

        $this->assertStringNotContainsString('<i>', $html);
        $this->assertStringContainsString('&lt;i&gt;x&lt;/i&gt;', $html);
        $this->assertStringContainsString('%22%3E%3Ci%3Ex%3C%2Fi%3E%20a%26b.pdf', $html);
    }

    public function testMultiKeyLabelsAreEscaped(): void
    {
        $field = new class (['name' => 'tags', 'label' => 'Tags', 'table' => 't']) extends FieldMultiKey {
            protected ?array $listLabels = [1 => self::PAYLOAD_LABEL];
            public const PAYLOAD_LABEL = '<i>x</i>';
        };
        $field->tablePk = 'id';

        $html = $field->show(['id' => 1]);

        $this->assertStringNotContainsString('<i>', $html);
        $this->assertStringContainsString('&lt;i&gt;x&lt;/i&gt;', $html);
    }

    public function testVirtualEscapesRowValuesButKeepsDeveloperTemplate(): void
    {
        $row = ['v' => '<i>v</i>', 'name' => '<b>n</b>&x'];
        $text = new FieldVirtual([
            'name' => 'v', 'label' => 'V', 'table' => 't',
            'params' => ['main' => ['text' => '<em>{name}</em>', 'href' => '', 'in_modal' => false]],
        ]);
        $this->assertSame('<em>&lt;b&gt;n&lt;/b&gt;&amp;x</em>', $text->show($row));

        $plain = new FieldVirtual(['name' => 'v', 'label' => 'V', 'table' => 't', 'params' => ['main' => ['text' => '', 'href' => '']]]);
        $this->assertSame('&lt;i&gt;v&lt;/i&gt;', $plain->show($row));

        $link = new FieldVirtual([
            'name' => 'v', 'label' => 'V', 'table' => 't',
            'params' => ['main' => ['text' => 'go', 'href' => '/p?a={name}&b=1', 'in_modal' => false]],
        ]);
        $html = $link->show($row);
        $this->assertStringContainsString('href="/p?a=%3Cb%3En%3C%2Fb%3E%26x&amp;b=1"', $html);
        $this->assertStringContainsString('>go</a>', $html);
    }

    public function testEnumDetailIsEscaped(): void
    {
        $field = new class (['name' => 'st', 'label' => 'St', 'table' => 't']) extends FieldEnum {
            protected function fetchValues(): array
            {
                return ['a' => '<i>x</i>'];
            }
        };

        $this->assertSame('&lt;i&gt;x&lt;/i&gt;', $field->showDetail(['st' => 'a']));
        $this->assertSame('', $field->showDetail(['st' => 'zzz']));
    }
}
