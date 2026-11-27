<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Base\Form\FormCallback\FormCallbackState;
use Simflex\Core\Layout\Components\Base\Form\FormCallback\Layout as FormCallback;
use Simflex\Core\Layout\Components\Base\Form\FormMailing\Layout as FormMailing;
use Simflex\Core\Layout\Components\Base\Modal\ModalMain\Layout as ModalMain;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Base/Form и Base/Modal (страница Base в Figma): подписка на рассылку и окно в потоке страницы (inline)
 */
class BaseFormModalTest extends Unit
{
    private mixed $configBackup;

    protected function _before(): void
    {
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => true]);
        Container::set('location', Location::Site);
    }

    protected function _after(): void
    {
        Container::set('config', $this->configBackup);
    }

    public function testMailingIsCallbackWithRequiredEmailOnly(): void
    {
        $html = FormMailing::drawFormMailing(guard: false);
        $this->assertStringContainsString('callback mailing', str_replace(['callback-layout_grid ', 'callback-state_default ', 'callback-theme_light '], '', $html));
        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('required', $html);
        $this->assertStringNotContainsString('name="phone"', $html);
        $this->assertStringNotContainsString('name="name"', $html);
        $this->assertStringNotContainsString('name="comment"', $html);
        $this->assertStringContainsString('value="mailing"', $html);
        $this->assertStringContainsString('action="/form/mailing"', $html);
        $this->assertStringContainsString('Подписаться', $html);
        $this->assertStringContainsString('Вы подписаны', $html);
        $this->assertStringContainsString('name="consent"', $html);
        $this->assertStringNotContainsString('name="consent"', FormMailing::drawFormMailing(consent: false, guard: false));
    }

    public function testCallbackEmailIsOptionalByDefault(): void
    {
        $html = FormCallback::drawFormCallback(guard: false);
        $this->assertStringContainsString('Почта', $html);
        $this->assertStringNotContainsString('Почта*', $html);
        $this->assertStringContainsString('Почта*', FormCallback::drawFormCallback(emailRequired: true, guard: false));
        $sending = FormCallback::drawFormCallback(state: FormCallbackState::Sending, guard: false);
        $this->assertStringContainsString('aria-busy="true"', $sending);
    }

    public function testModalInlineIsVisibleAndRegularIsHidden(): void
    {
        $hidden = ModalMain::drawModalMain(id: 'm1', title: 'Окно');
        $this->assertMatchesRegularExpression('/id="m1"[^>]*\shidden/', $hidden);
        $this->assertStringNotContainsString('modal_inline', $hidden);

        $inline = ModalMain::drawModalMain(id: 'm2', title: 'Окно', inline: true);
        $this->assertStringContainsString('modal_inline', $inline);
        $this->assertDoesNotMatchRegularExpression('/id="m2"[^>]*\shidden/', $inline);
        $this->assertStringContainsString('role="dialog"', $inline);
        $this->assertStringContainsString('role="alertdialog"', ModalMain::drawModalMain(id: 'm3', alert: true));
    }
}
