<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Http\Request;
use Simflex\Core\Layout\Components\Base\Form\FormCallback\FormCallbackLayout;
use Simflex\Core\Layout\Components\Base\Form\FormCallback\FormCallbackState;
use Simflex\Core\Layout\Components\Base\Form\FormCallback\Layout as Callback;
use Simflex\Core\Layout\Components\Base\Form\FormResult\Layout as Result;
use Simflex\Core\Layout\Components\Base\Form\FormResult\FormResultStyle;
use Simflex\Core\Layout\Components\Base\Form\FormResult\FormResultType;
use Simflex\Core\Layout\Components\Base\Modal\ModalMain\Layout as Modal;
use Simflex\Core\Layout\Components\Base\Modal\ModalMain\ModalMainSize;
use Simflex\Core\System\Container;

/**
 * Отрисовка Base/Modal/ModalMain, Base/Form/FormCallback, Base/Form/FormResult
 */
class FormComponentsTest extends Unit
{
    private mixed $config = null;
    private mixed $request = null;

    protected function _before(): void
    {
        $this->config = Container::get('config');
        $this->request = Container::isSet('request') ? Container::get('request') : null;
        Container::set('config', (object)['theme' => 'default', 'db' => ['name' => 'n', 'password' => 'p']]);
        Container::set('request', Request::create('/page'));
    }

    protected function _after(): void
    {
        Container::set('config', $this->config);
        Container::set('request', $this->request);
    }

    public function testModalDialogAttributes(): void
    {
        $html = Modal::drawModalMain(id: 'callback', title: 'Заголовок <b>', text: 'Текст', content: '<p>тело</p>', size: ModalMainSize::Small);

        $this->assertStringContainsString('id="callback"', $html);
        $this->assertStringContainsString('modal-size_s', $html);
        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
        $this->assertStringContainsString('aria-labelledby="callback-title"', $html);
        $this->assertStringContainsString('aria-describedby="callback-text"', $html);
        $this->assertStringContainsString('Заголовок &lt;b&gt;', $html);
        $this->assertStringContainsString('<p>тело</p>', $html);
        $this->assertStringContainsString('aria-label="Закрыть"', $html);
        $this->assertMatchesRegularExpression('/<div id="callback"[^>]* hidden/', $html);
    }

    public function testResultRolesAndTypes(): void
    {
        $ok = Result::drawFormResult(title: 'Готово', style: FormResultStyle::Success);
        $fail = Result::drawFormResult(title: 'Ошибка', style: FormResultStyle::Error, type: FormResultType::Vertical, buttonText: '');

        $this->assertStringContainsString('role="status"', $ok);
        $this->assertStringContainsString('result-type_horizontal', $ok);
        $this->assertStringContainsString('result__button', $ok);
        $this->assertStringContainsString('role="alert"', $fail);
        $this->assertStringContainsString('result-type_vertical', $fail);
        $this->assertStringNotContainsString('result__button', $fail);
    }

    public function testCallbackFieldsConsentGuardAndTemplates(): void
    {
        $html = Callback::drawFormCallback(id: 'cb', layout: FormCallbackLayout::Stack, values: ['name' => 'А"Б']);

        $this->assertStringContainsString('callback-layout_stack', $html);
        $this->assertStringContainsString('novalidate', $html);
        $this->assertStringContainsString('action="/form/callback"', $html);
        foreach (['name', 'phone', 'email', 'comment', 'consent'] as $field) {
            $this->assertStringContainsString('name="' . $field . '"', $html);
        }
        $this->assertStringContainsString('maxlength="300"', $html);
        $this->assertStringContainsString('0/300', $html);
        $this->assertStringContainsString('required=""', $html);
        $this->assertStringContainsString('value="А&quot;Б"', $html);
        $this->assertStringContainsString('name="form_token"', $html);
        $this->assertStringContainsString('name="website"', $html);
        $this->assertStringContainsString('data-callback-template="success"', $html);
        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('type="submit"', $html);
    }

    public function testCallbackCanDisableFieldsAndShowsErrors(): void
    {
        $html = Callback::drawFormCallback(
            id: 'cb',
            email: false,
            comment: false,
            consent: false,
            guard: false,
            state: FormCallbackState::Error,
            errors: ['name' => 'Заполните поле'],
        );

        $this->assertStringNotContainsString('name="email"', $html);
        $this->assertStringNotContainsString('name="comment"', $html);
        $this->assertStringNotContainsString('name="consent"', $html);
        $this->assertStringNotContainsString('form_token', $html);
        $this->assertStringContainsString('callback-state_error', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('data-errors=', $html);
    }

    public function testCallbackSendingState(): void
    {
        $html = Callback::drawFormCallback(id: 'cb', state: FormCallbackState::Sending);

        $this->assertStringContainsString('callback-state_sending', $html);
        $this->assertStringContainsString('aria-busy="true"', $html);
        $this->assertStringContainsString('loading', $html);
    }
}
