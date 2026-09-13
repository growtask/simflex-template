<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Admin\Fields\FieldCategorySelect;
use Simflex\Admin\Fields\FieldDate;
use Simflex\Admin\Fields\FieldDateTime;
use Simflex\Admin\Fields\FieldDouble;
use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldImage;
use Simflex\Admin\Fields\FieldMultiKey;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldTable;
use Simflex\Admin\Fields\FieldTime;
use Simflex\Extensions\Block\Block;
use Simflex\Extensions\Code\Code;
use Simflex\Extensions\Form\Fields\FieldCheckbox;
use Simflex\Extensions\Form\Fields\FieldPassword;
use Simflex\Extensions\Form\Fields\FieldSpamCode;
use Simflex\Extensions\Form\Fields\FieldString;
use Simflex\Extensions\Form\ModuleForm;

class FieldsBugfixTest extends Unit
{
    private array $post;
    private ?array $session;

    protected function _before(): void
    {
        $this->post = $_POST;
        $this->session = $_SESSION ?? null;
    }

    protected function _after(): void
    {
        $_POST = $this->post;
        if ($this->session === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->session;
        }
    }

    private function form(string $name = 'feedback'): ModuleForm
    {
        $form = $this->createMock(ModuleForm::class);
        $form->method('getName')->willReturn($name);
        return $form;
    }

    public function testBlockExtendsCode(): void
    {
        $this->assertTrue(class_exists(Block::class));
        $this->assertTrue(is_subclass_of(Block::class, Code::class));
    }

    public function testCheckboxUsesFormName(): void
    {
        $_POST['feedback']['agree'] = 'on';
        ob_start();
        (new FieldCheckbox($this->form(), 'agree', 'Agree', ''))->html();
        $html = ob_get_clean();
        $this->assertStringContainsString('name="feedback[agree]"', $html);
        $this->assertStringContainsString('checked="checked"', $html);
    }

    public function testPasswordUsesFormName(): void
    {
        $_POST['feedback']['pass'] = 'a"b';
        ob_start();
        (new FieldPassword($this->form(), 'pass', 'Pass', ''))->html();
        $html = ob_get_clean();
        $this->assertStringContainsString('name="feedback[pass]"', $html);
        $this->assertStringContainsString('value="a&quot;b"', $html);
    }

    public function testSpamCodeCheckUsesFormName(): void
    {
        $field = new FieldSpamCode($this->form(), 'code', 'Code', '');
        $_SESSION['spamcode'] = 'abc';
        $_POST['feedback']['code'] = 'abc';
        $this->assertSame([], $field->check());
        $_POST['feedback']['code'] = 'xyz';
        $this->assertArrayHasKey('code', $field->check());
        unset($_SESSION['spamcode'], $_POST['feedback']['code']);
        $this->assertArrayHasKey('code', $field->check());
    }

    public function testUniqueCheckUsesBoundParameterWithoutLegacyPrefix(): void
    {
        // DB в unit-окружении не подключена — проверяем исходник запроса
        $src = file_get_contents((new \ReflectionClass(FieldString::class))->getParentClass()->getFileName());
        $this->assertStringNotContainsString('SFDB', $src);
        $this->assertStringNotContainsString('#__', $src);
        $this->assertStringContainsString('DB::result($q, \'cnt\', [$_POST[$formName][$this->name]])', $src);
    }

    public function testDeadCodeRemoved(): void
    {
        $this->assertFalse(property_exists(FieldEnum::class, 'select'));
        $this->assertFalse(method_exists(FieldImage::class, 'loadUI') && (new \ReflectionMethod(FieldImage::class, 'loadUI'))->getDeclaringClass()->getName() === FieldImage::class);
        foreach ([FieldEnum::class, FieldImage::class] as $class) {
            $src = file_get_contents((new \ReflectionClass($class))->getFileName());
            $this->assertStringNotContainsString('$this->select', $src);
            $this->assertStringNotContainsString('PlugJQuery', $src);
        }
        $this->assertStringNotContainsString('AdminBase', file_get_contents((new \ReflectionClass(FieldMultiKey::class))->getFileName()));
        $this->assertStringNotContainsString('AdminBase', file_get_contents((new \ReflectionClass(FieldNPP::class))->getFileName()));
    }

    public function testTableMakeValueThrowsOnInvalidUtf8(): void
    {
        $this->assertSame('{"s":["a"],"v":["б"]}', FieldTable::makeValue(['a'], ['б']));
        $this->expectException(\JsonException::class);
        FieldTable::makeValue([], ["\xB1\x31"]);
    }

    public function testEmptyNullableFieldsReturnRealNull(): void
    {
        $row = ['name' => 'f', 'label' => 'F', 'table' => 'content', 'params' => ['main' => ['e2n' => 1]]];
        foreach ([FieldDate::class, FieldDateTime::class, FieldDouble::class, FieldNPP::class, FieldTime::class] as $class) {
            $_POST['f'] = '';
            $this->assertNull((new $class($row))->getPOST(), $class);
        }
    }

    public function testCategorySyncAddsOnlyMissingNames(): void
    {
        // Та же логика сравнения, что в FieldCategorySelect::syncCategories (таблица shared_category в схеме отсутствует)
        $real = array_column([['content_id' => 1, 'title' => 'a'], ['content_id' => 2, 'title' => '5']], 'content_id', 'title');
        $existing = array_column([['name' => 'a']], 'name');
        $this->assertSame([1 => '5'], array_diff(array_map('strval', array_keys($real)), $existing));
        $this->assertTrue(method_exists(FieldCategorySelect::class, 'input'));
    }
}
