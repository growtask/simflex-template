<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\System\Container;
use Simflex\Core\Layout\Components\UI\Link\LinkContact\LinkContactSize;
use Simflex\Core\Layout\Components\UI\Link\LinkContact\LinkContactStyle;
use Simflex\Core\Layout\Components\UI\Link\LinkContact\LinkContactType;
use Simflex\Core\Layout\Components\UI\Link\LinkContact\Layout as Contact;
use Simflex\Core\Layout\Components\UI\Link\LinkMain\Layout as Link;
use Simflex\Core\Layout\Components\UI\Link\LinkMain\LinkMainSize;
use Simflex\Core\Layout\Components\UI\Link\LinkMain\LinkMainStyle;
use Simflex\Core\Layout\Components\UI\Button\ButtonSocialSmall\Layout as Social;
use Simflex\Core\Layout\Components\UI\Button\ButtonSocialSmall\ButtonSocialSmallNetwork;
use Simflex\Core\Layout\Components\UI\Button\ButtonSocialSmall\ButtonSocialSmallStyle;
use Simflex\Core\Layout\Components\UI\Other\StepIndicator\Layout as StepIndicator;
use Simflex\Core\Layout\Components\UI\Other\StepIndicator\StepIndicatorSize;
use Simflex\Core\Layout\Components\UI\Textarea\Layout as Textarea;
use Simflex\Core\Layout\Components\UI\Textarea\TextareaLabelPos;
use Simflex\Core\Layout\Components\UI\Textarea\TextareaSize;
use Simflex\Core\Layout\Components\UI\Textarea\TextareaState;
use Simflex\Core\Layout\Components\UI\Textarea\TextareaStyle;

/**
 * Отрисовка новых компонентов UI: классы, атрибуты, экранирование, enum
 */
class UiCoreComponentsTest extends Unit
{
    private mixed $config = null;

    protected function _before(): void
    {
        // Иконки берут тему из конфига сайта
        $this->config = Container::get('config');
        Container::set('config', (object)['theme' => 'default']);
    }

    protected function _after(): void
    {
        Container::set('config', $this->config);
    }

    public function testTextareaClassesCounterAndAria(): void
    {
        $html = Textarea::drawTextarea(
            className: 'extra',
            value: 'абв',
            labelText: 'Комментарий',
            message: 'Слишком коротко',
            id: 'c1',
            style: TextareaStyle::Outline,
            size: TextareaSize::Large,
            state: TextareaState::Error,
            attributes: ['name' => 'comment']
        );

        $this->assertStringContainsString('textarea-style_outline', $html);
        $this->assertStringContainsString('textarea-size_l', $html);
        $this->assertStringContainsString('textarea-state_error', $html);
        $this->assertStringContainsString('extra', $html);
        $this->assertStringContainsString('name="comment"', $html);
        $this->assertStringContainsString('maxlength="300"', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('aria-describedby="c1-message c1-counter"', $html);
        $this->assertStringContainsString('for="c1"', $html);
        $this->assertStringContainsString('3/300', $html);
    }

    public function testTextareaEscapesValueAndHiddenLabelBecomesAria(): void
    {
        $html = Textarea::drawTextarea(
            value: '</textarea><script>x</script>',
            labelText: 'Поле "A"',
            counter: false,
            labelPos: TextareaLabelPos::Hidden
        );

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;/textarea&gt;', $html);
        $this->assertStringContainsString('aria-label="Поле &quot;A&quot;"', $html);
        $this->assertStringNotContainsString('textarea__counter', $html);
        $this->assertStringNotContainsString('<label', $html);
    }

    public function testLinkExternalAndEscaping(): void
    {
        $html = Link::drawLinkMain(
            text: '<b>Подробнее</b>',
            href: '/a?x=1&y=2',
            external: true,
            style: LinkMainStyle::Underline,
            size: LinkMainSize::ExtraLarge
        );

        $this->assertStringContainsString('link-underline', $html);
        $this->assertStringContainsString('link-size_xl', $html);
        $this->assertStringContainsString('&lt;b&gt;Подробнее&lt;/b&gt;', $html);
        $this->assertStringContainsString('href="/a?x=1&amp;y=2"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener"', $html);
        $this->assertStringContainsString('visually-hidden', $html);
    }

    public function testLinkInternalHasNoTarget(): void
    {
        $html = Link::drawLinkMain(text: 'Каталог', href: '/catalog/', iconRight: 'chevron-s-right');

        $this->assertStringNotContainsString('target=', $html);
        $this->assertStringContainsString('link__icon-right', $html);
        $this->assertStringContainsString('#icon-chevron-s-right', $html);
    }

    public function testContactBuildsTelAndMailto(): void
    {
        $phone = Contact::drawLinkContact(text: '+7 (999) 123-45-67', value: '+7 (999) 123-45-67');
        $this->assertStringContainsString('href="tel:+79991234567"', $phone);
        $this->assertStringContainsString('+7 (999) 123-45-67</span>', $phone);

        $mail = Contact::drawLinkContact(
            value: 'info@example.com',
            type: LinkContactType::Email,
            style: LinkContactStyle::Filled,
            size: LinkContactSize::Large
        );
        $this->assertStringContainsString('href="mailto:info@example.com"', $mail);
        $this->assertStringContainsString('contact-filled', $mail);
        $this->assertStringContainsString('contact-size_l', $mail);
        $this->assertStringContainsString('info@example.com</span>', $mail);
    }

    public function testSocialLabelIconsAndRel(): void
    {
        $html = Social::drawButtonSocialSmall(ButtonSocialSmallNetwork::Vkontakte, 'https://vk.com/x');

        $this->assertStringContainsString('aria-label="VK"', $html);
        $this->assertStringContainsString('social-network_vkontakte', $html);
        $this->assertStringContainsString('#icon-social-vkontakte', $html);
        $this->assertStringContainsString('#icon-social-monochrome-vkontakte', $html);
        $this->assertStringContainsString('rel="noopener"', $html);

        $brand = Social::drawButtonSocialSmall(ButtonSocialSmallNetwork::YandexZen, '/z', label: 'Наш Дзен', style: ButtonSocialSmallStyle::Brand);
        $this->assertStringContainsString('aria-label="Наш Дзен"', $brand);
        $this->assertStringContainsString('social-network_yandex-zen', $brand);
        $this->assertStringNotContainsString('#icon-social-yandex-zen"', $brand);
        $this->assertStringContainsString('#icon-social-monochrome-yandex-zen', $brand);
    }

    public function testStepIndicatorCountAndActive(): void
    {
        $html = StepIndicator::drawStepIndicator(count: 5, active: 2, label: 'Слайды', size: StepIndicatorSize::Small);

        $this->assertSame(5, substr_count($html, 'class="step-indicator__item'));
        $this->assertSame(1, substr_count($html, 'step-indicator__item_active'));
        $this->assertStringContainsString('step-indicator-size_s', $html);
        $this->assertStringContainsString('aria-label="Слайды"', $html);
        $this->assertStringContainsString('3 / 5', $html);

        // Активная полоса не выходит за пределы
        $clamped = StepIndicator::drawStepIndicator(count: 3, active: 99);
        $this->assertStringContainsString('data-active="2"', $clamped);
    }
}
