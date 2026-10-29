<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\System\Container;
use Simflex\Core\Layout\Components\UI\Core\Contact\ContactSize;
use Simflex\Core\Layout\Components\UI\Core\Contact\ContactStyle;
use Simflex\Core\Layout\Components\UI\Core\Contact\ContactType;
use Simflex\Core\Layout\Components\UI\Core\Contact\Layout as Contact;
use Simflex\Core\Layout\Components\UI\Core\Link\Layout as Link;
use Simflex\Core\Layout\Components\UI\Core\Link\LinkSize;
use Simflex\Core\Layout\Components\UI\Core\Link\LinkStyle;
use Simflex\Core\Layout\Components\UI\Core\Logo\Layout as Logo;
use Simflex\Core\Layout\Components\UI\Core\Logo\LogoType;
use Simflex\Core\Layout\Components\UI\Core\Social\Layout as Social;
use Simflex\Core\Layout\Components\UI\Core\Social\SocialNetwork;
use Simflex\Core\Layout\Components\UI\Core\Social\SocialStyle;
use Simflex\Core\Layout\Components\UI\Core\StepIndicator\Layout as StepIndicator;
use Simflex\Core\Layout\Components\UI\Core\StepIndicator\StepIndicatorSize;
use Simflex\Core\Layout\Components\UI\Core\Textarea\Layout as Textarea;
use Simflex\Core\Layout\Components\UI\Core\Textarea\TextareaLabelPos;
use Simflex\Core\Layout\Components\UI\Core\Textarea\TextareaSize;
use Simflex\Core\Layout\Components\UI\Core\Textarea\TextareaState;
use Simflex\Core\Layout\Components\UI\Core\Textarea\TextareaStyle;

/**
 * Отрисовка новых компонентов UI/Core: классы, атрибуты, экранирование, enum
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
        $html = Link::drawLink(
            text: '<b>Подробнее</b>',
            href: '/a?x=1&y=2',
            external: true,
            style: LinkStyle::Underline,
            size: LinkSize::ExtraLarge
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
        $html = Link::drawLink(text: 'Каталог', href: '/catalog/', iconRight: 'chevron-s-right');

        $this->assertStringNotContainsString('target=', $html);
        $this->assertStringContainsString('link__icon-right', $html);
        $this->assertStringContainsString('#icon-chevron-s-right', $html);
    }

    public function testContactBuildsTelAndMailto(): void
    {
        $phone = Contact::drawContact(text: '+7 (999) 123-45-67', value: '+7 (999) 123-45-67');
        $this->assertStringContainsString('href="tel:+79991234567"', $phone);
        $this->assertStringContainsString('+7 (999) 123-45-67</span>', $phone);

        $mail = Contact::drawContact(
            value: 'info@example.com',
            type: ContactType::Email,
            style: ContactStyle::Filled,
            size: ContactSize::Large
        );
        $this->assertStringContainsString('href="mailto:info@example.com"', $mail);
        $this->assertStringContainsString('contact-filled', $mail);
        $this->assertStringContainsString('contact-size_l', $mail);
        $this->assertStringContainsString('info@example.com</span>', $mail);
    }

    public function testSocialLabelIconsAndRel(): void
    {
        $html = Social::drawSocial(SocialNetwork::Vkontakte, 'https://vk.com/x');

        $this->assertStringContainsString('aria-label="VK"', $html);
        $this->assertStringContainsString('social-network_vkontakte', $html);
        $this->assertStringContainsString('#icon-social-vkontakte', $html);
        $this->assertStringContainsString('#icon-social-monochrome-vkontakte', $html);
        $this->assertStringContainsString('rel="noopener"', $html);

        $brand = Social::drawSocial(SocialNetwork::YandexZen, '/z', label: 'Наш Дзен', style: SocialStyle::Brand);
        $this->assertStringContainsString('aria-label="Наш Дзен"', $brand);
        $this->assertStringContainsString('social-network_yandex-zen', $brand);
        $this->assertStringNotContainsString('#icon-social-yandex-zen"', $brand);
        $this->assertStringContainsString('#icon-social-monochrome-yandex-zen', $brand);
    }

    public function testLogoAnimatedAndImage(): void
    {
        $html = Logo::drawLogo(name: 'Simflex <CMS>');
        $this->assertStringContainsString('logo-type_anim', $html);
        $this->assertStringContainsString('href="/"', $html);
        $this->assertStringContainsString('aria-label="Simflex &lt;CMS&gt;"', $html);
        $this->assertStringContainsString('logo__slide', $html);
        $this->assertStringContainsString('images/brand/logo.svg', $html);
        $this->assertStringContainsString('Главная', $html);
        $this->assertStringNotContainsString('logo__caption', $html);

        $caption = Logo::drawLogo(name: 'Simflex', caption: 'Shop');
        $this->assertStringContainsString('<span class="logo__caption">Shop</span>', $caption);
        $noText = Logo::drawLogo(name: 'Simflex', caption: 'Shop', text: false);
        $this->assertStringNotContainsString('logo__caption', $noText);

        $madeIn = Logo::drawLogo(name: 'Студия', type: LogoType::MadeIn);
        $this->assertStringContainsString('Создано', $madeIn);
        $this->assertStringContainsString('images/brand/made-in.svg', $madeIn);
        $madeBy = Logo::drawLogo(name: 'Движок', type: LogoType::MadeBy, theme: \Simflex\Core\Layout\Components\UI\Core\Logo\LogoTheme::Dark);
        $this->assertStringContainsString('На базе', $madeBy);
        $this->assertStringContainsString('images/brand/made-by-dark.svg', $madeBy);

        $img = Logo::drawLogo(
            href: 'https://studio.example',
            name: 'Студия',
            src: '/assets/default/images/made-in.svg',
            external: true,
            type: LogoType::MadeIn
        );
        $this->assertStringContainsString('logo-type_made-in', $img);
        $this->assertStringContainsString('<img class="logo__image" src="/assets/default/images/made-in.svg" alt="Студия">', $img);
        $this->assertStringContainsString('target="_blank"', $img);
        $this->assertStringNotContainsString('logo__slide', $img);
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
