<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Parts\Delivery\DeliveryList\Layout as DeliveryList;
use Simflex\Core\Layout\Components\Parts\Delivery\DeliveryLogo\DeliveryService;
use Simflex\Core\Layout\Components\Parts\Delivery\DeliveryLogo\DeliveryStyle;
use Simflex\Core\Layout\Components\Parts\Delivery\DeliveryLogo\Layout as DeliveryLogo;
use Simflex\Core\Layout\Components\Parts\Payment\PaymentList\Layout as PaymentList;
use Simflex\Core\Layout\Components\Parts\Payment\PaymentLogo\Layout as PaymentLogo;
use Simflex\Core\Layout\Components\Parts\Payment\PaymentLogo\PaymentStyle;
use Simflex\Core\Layout\Components\Parts\Payment\PaymentLogo\PaymentSystem;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Parts/Payment и Parts/Delivery (блоки Payment и Delivery в Figma): знак и ряд знаков
 */
class PartsPaymentDeliveryTest extends Unit
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

    public function testPaymentLogoStylesPickFile(): void
    {
        $color = PaymentLogo::drawPaymentLogo(PaymentSystem::Visa);
        $this->assertStringContainsString('payment-logo payment-logo_style_color', $color);
        $this->assertStringContainsString('images/payment/visa.svg', $color);
        $this->assertStringContainsString('alt="Visa"', $color);
        $this->assertStringContainsString('<span', $color);
        $this->assertStringContainsString('images/payment/mir-mono.svg', PaymentLogo::drawPaymentLogo(PaymentSystem::Mir, PaymentStyle::Monochrome));
        $this->assertStringContainsString(
            'images/payment/google-pay-dark.svg',
            PaymentLogo::drawPaymentLogo(PaymentSystem::GooglePay, PaymentStyle::MonochromeDark)
        );
        $custom = PaymentLogo::drawPaymentLogo(PaymentSystem::Qiwi, label: 'Кошелёк', src: '/assets/default/images/brand/logo.svg', className: 'footer__pay');
        $this->assertStringContainsString('alt="Кошелёк"', $custom);
        $this->assertStringContainsString('src="/assets/default/images/brand/logo.svg"', $custom);
        $this->assertStringContainsString('footer__pay', $custom);
    }

    public function testLogoWithHrefIsLink(): void
    {
        $html = DeliveryLogo::drawDeliveryLogo(DeliveryService::Cdek, href: '/delivery/cdek');
        $this->assertStringContainsString('<a ', $html);
        $this->assertStringContainsString('href="/delivery/cdek"', $html);
        $this->assertStringContainsString('alt="СДЭК"', $html);
    }

    public function testDeliveryLogoFilesExist(): void
    {
        $dir = dirname(__DIR__, 3) . '/public/assets/default/images/delivery/';
        foreach (DeliveryService::cases() as $service) {
            foreach (DeliveryStyle::cases() as $style) {
                $this->assertFileExists($dir . $service->value . $style->suffix() . '.svg');
            }
        }
        $dir = dirname(__DIR__, 3) . '/public/assets/default/images/payment/';
        foreach (PaymentSystem::cases() as $system) {
            foreach (PaymentStyle::cases() as $style) {
                $this->assertFileExists($dir . $system->value . $style->suffix() . '.svg');
            }
        }
    }

    public function testMissingImageIsNotRendered(): void
    {
        // в теме без файлов знаков ничего не выводится: ни знак, ни ряд
        Container::set('config', (object)['theme' => 'no-such-theme', 'devMode' => true]);
        $this->assertSame('', PaymentLogo::drawPaymentLogo(PaymentSystem::Visa));
        $this->assertSame('', DeliveryLogo::drawDeliveryLogo(DeliveryService::Ozon, DeliveryStyle::Monochrome));
        $this->assertSame('', PaymentList::drawPaymentList());
        $this->assertSame('', DeliveryList::drawDeliveryList([DeliveryService::Cdek]));
        // свой src тоже проверяется: файла нет - ничего не выводится
        $this->assertSame('', PaymentLogo::drawPaymentLogo(PaymentSystem::Visa, src: '/x.svg'));
    }

    public function testListShowsAllOrSelected(): void
    {
        $all = PaymentList::drawPaymentList();
        $this->assertStringContainsString('payment-list', $all);
        $this->assertStringContainsString('aria-label="Способы оплаты"', $all);
        $this->assertSame(count(PaymentSystem::cases()), substr_count($all, 'payment-list__item'));

        $some = DeliveryList::drawDeliveryList([DeliveryService::Ozon, DeliveryService::Cdek], DeliveryStyle::Monochrome);
        $this->assertSame(2, substr_count($some, 'delivery-list__item'));
        $this->assertStringContainsString('images/delivery/ozon-mono.svg', $some);
        $this->assertLessThan(strpos($some, 'cdek-mono'), strpos($some, 'ozon-mono'));
        $this->assertStringNotContainsString('5post', $some);
    }
}
