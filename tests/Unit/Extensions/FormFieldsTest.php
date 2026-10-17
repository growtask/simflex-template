<?php

namespace Tests\Unit\Extensions;

use Codeception\Test\Unit;
use Simflex\Extensions\Form\Fields\FieldCheckbox;
use Simflex\Extensions\Form\Fields\FieldPassword;
use Simflex\Extensions\Form\Fields\FieldSpamCode;
use Simflex\Extensions\Form\Fields\FieldString;
use Simflex\Extensions\Form\ModuleForm;
use Tests\Support\MockDb;

/**
 * The fields of the site form module: the values are posted under the form's name
 */
class FormFieldsTest extends Unit
{
    use MockDb;

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

    public function testCheckbox(): void
    {
        $_POST['feedback']['agree'] = 'on';
        $html = (new FieldCheckbox($this->form(), 'agree', 'Agree', ''))->html();
        $this->assertStringContainsString('name="feedback[agree]"', $html);
        $this->assertStringContainsString('checked="checked"', $html);
    }

    public function testPassword(): void
    {
        $_POST['feedback']['pass'] = 'a"b';
        $html = (new FieldPassword($this->form(), 'pass', 'Pass', ''))->html();
        $this->assertStringContainsString('name="feedback[pass]"', $html);
        $this->assertStringContainsString('value="a&quot;b"', $html);
    }

    public function testSpamCode(): void
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

    public function testUniqueValue(): void
    {
        $field = new FieldString($this->form(), 'email', 'E-mail', '');
        $field->vUnique = ['key' => 'user.email'];
        $_POST['feedback']['email'] = "a'b@example.test";

        $db = $this->mockDb();
        $db->expects($this->exactly(2))->method('query')
            ->with('SELECT COUNT(*) cnt FROM `user` WHERE `email` = ?', ["a'b@example.test"])
            ->willReturn(true);
        $db->method('result')->willReturnOnConsecutiveCalls(0, 1);

        $this->assertSame([], $field->check());
        $this->assertSame(['email' => ['занято']], $field->check());
    }

    private function form(): ModuleForm
    {
        $form = $this->createMock(ModuleForm::class);
        $form->method('getName')->willReturn('feedback');
        return $form;
    }
}
