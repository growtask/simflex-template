<?php

namespace Tests\Unit\Extensions;

use Codeception\Test\Unit;
use Simflex\Core\Extensions\Form\Fields\FieldCheckbox;
use Simflex\Core\Extensions\Form\Fields\FieldPassword;
use Simflex\Core\Extensions\Form\Fields\FieldSpamCode;
use Simflex\Core\Extensions\Form\Fields\FieldString;
use Simflex\Core\Extensions\Form\ModuleForm;
use Simflex\Core\Http\Request;
use Simflex\Core\System\Container;
use Simflex\Core\System\Session;
use Tests\Support\MockDb;

/**
 * Поля модуля форм сайта: значения отправляются под именем формы
 */
class FormFieldsTest extends Unit
{
    use MockDb;

    private mixed $requestBackup = null;

    protected function _before(): void
    {
        $this->requestBackup = Container::getRequest();
        // Session::set() пытается начать сессию, но в CLI заголовки уже отправлены: глушим только это
        set_error_handler(
            static fn(int $no, string $msg) => str_contains($msg, 'ession') || str_contains($msg, 'headers'),
            E_WARNING | E_NOTICE
        );
    }

    protected function _after(): void
    {
        restore_error_handler();
        Container::set('request', $this->requestBackup);
        Session::set('spamcode', null);
    }

    /**
     * Отправленные значения формы feedback
     */
    private function post(array $values): void
    {
        Container::set('request', Request::create('/', [], ['feedback' => $values]));
    }

    public function testCheckbox(): void
    {
        $this->post(['agree' => 'on']);
        $html = (new FieldCheckbox($this->form(), 'agree', 'Agree', ''))->html();
        $this->assertStringContainsString('name="feedback[agree]"', $html);
        $this->assertStringContainsString('checked="checked"', $html);
    }

    public function testPassword(): void
    {
        $this->post(['pass' => 'a"b']);
        $html = (new FieldPassword($this->form(), 'pass', 'Pass', ''))->html();
        $this->assertStringContainsString('name="feedback[pass]"', $html);
        $this->assertStringContainsString('value="a&quot;b"', $html);
    }

    public function testSpamCode(): void
    {
        $field = new FieldSpamCode($this->form(), 'code', 'Code', '');
        Session::set('spamcode', 'abc');
        $this->post(['code' => 'abc']);
        $this->assertSame([], $field->check());
        $this->post(['code' => 'xyz']);
        $this->assertArrayHasKey('code', $field->check());
        Session::set('spamcode', null);
        $this->post([]);
        $this->assertArrayHasKey('code', $field->check());
    }

    public function testUniqueValue(): void
    {
        $field = new FieldString($this->form(), 'email', 'E-mail', '');
        $field->vUnique = ['key' => 'user.email'];
        $this->post(['email' => "a'b@example.test"]);

        $this->mockRows([[0], [1]], $queries);

        $this->assertSame([], $field->check());
        $this->assertSame(['email' => ['занято']], $field->check());
        // значение - параметром запроса
        $this->assertStringContainsString('FROM `user`', $queries[0][0]);
        $this->assertStringNotContainsString("a'b", $queries[0][0]);
        $this->assertSame(["a'b@example.test"], array_values($queries[0][1]));
    }

    private function form(): ModuleForm
    {
        $form = $this->createMock(ModuleForm::class);
        $form->method('getName')->willReturn('feedback');
        return $form;
    }
}
