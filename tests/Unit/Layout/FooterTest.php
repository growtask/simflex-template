<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Http\Menu as SiteMenu;
use Simflex\Core\Layout\Components\Common\Footer\Layout as Footer;
use Simflex\Core\System\Container;

/**
 * Подвал: контакты, соцсети, колонки меню из таблицы menu, нижняя строка
 */
class FooterTest extends Unit
{
    private mixed $config = null;
    private ?array $menuTree = null;

    protected function _before(): void
    {
        // Иконки берут тему из конфига сайта
        $this->config = Container::get('config');
        Container::set('config', (object)['theme' => 'default']);
        $this->menuTree = (new \ReflectionProperty(SiteMenu::class, 'tree'))->getValue();
    }

    protected function _after(): void
    {
        Container::set('config', $this->config);
        (new \ReflectionProperty(SiteMenu::class, 'tree'))->setValue(null, $this->menuTree);
    }

    protected function draw(array $args = []): string
    {
        return preg_replace('/\s+/', ' ', Footer::drawFooter(...$args + [
            'columns' => [],
            'phone' => '',
            'email' => '',
            'socials' => [],
            'company' => 'ООО Ромашка',
            'logoName' => 'Simflex',
            'year' => 2026,
        ]));
    }

    public function testBottomLineAndModalButton(): void
    {
        $html = $this->draw(['className' => 'extra']);

        $this->assertStringContainsString('class="footer extra"', $html);
        $this->assertStringContainsString('© ООО Ромашка 2026', $html);
        $this->assertStringContainsString('data-modal="callback"', $html);
        $this->assertStringContainsString('Связаться с нами', $html);
        $this->assertStringContainsString('logo-type_made-in', $html);
        $this->assertStringContainsString('logo-type_made-by', $html);
        // Без колонок нет ни меню, ни вертикальной линии, без контактов нет блока контактов
        $this->assertStringNotContainsString('footer__menu', $html);
        $this->assertStringNotContainsString('separator-orientation_vertical', $html);
        $this->assertStringNotContainsString('footer__contacts', $html);

        $this->assertStringNotContainsString('data-modal', $this->draw(['modal' => '']));
    }

    public function testContactsAndSocials(): void
    {
        $html = $this->draw([
            'phone' => '8 800 555-35-35',
            'email' => 'mail@example.com',
            'socials' => ['telegram' => 'https://t.me/x', 'vkontakte' => ''],
        ]);

        $this->assertStringContainsString('href="tel:88005553535"', $html);
        $this->assertStringContainsString('href="mailto:mail@example.com"', $html);
        $this->assertStringContainsString('href="https://t.me/x"', $html);
        // Пустой адрес - сеть не выводится
        $this->assertSame(1, substr_count($html, 'class="social '));
    }

    public function testColumnsAreEscapedAndActiveLinkMarked(): void
    {
        $html = $this->draw(['columns' => [[
            'title' => 'Информация:',
            'links' => [
                ['text' => 'FAQ <b>', 'href' => '/faq/', 'active' => true],
                ['text' => 'Контакты', 'href' => '/contacts/'],
            ],
        ]]]);

        $this->assertStringContainsString('footer__column-title">Информация:</p>', $html);
        $this->assertStringContainsString('FAQ &lt;b&gt;', $html);
        $this->assertStringNotContainsString('FAQ <b>', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertSame(1, substr_count($html, 'aria-current'));
        $this->assertStringContainsString('tab-flat', $html);
        $this->assertStringContainsString('tab-size_s', $html);
    }

    public function testMenuColumnsFromFooterPlace(): void
    {
        (new \ReflectionProperty(SiteMenu::class, 'tree'))->setValue(null, [
            0 => [
                1 => ['menu_id' => 1, 'menu_pid' => 0, 'place' => 'main', 'name' => 'Главная', 'link' => '/'],
                2 => ['menu_id' => 2, 'menu_pid' => 0, 'place' => 'footer', 'name' => 'Информация', 'link' => ''],
                3 => ['menu_id' => 3, 'menu_pid' => 0, 'place' => 'footer', 'name' => 'Пустая', 'link' => ''],
                4 => ['menu_id' => 4, 'menu_pid' => 0, 'place' => 'footer', 'name' => 'Скрытая', 'link' => '', 'hidden' => 1],
            ],
            2 => [
                5 => ['menu_id' => 5, 'menu_pid' => 2, 'place' => 'main', 'name' => 'FAQ', 'link' => '/faq/'],
                6 => ['menu_id' => 6, 'menu_pid' => 2, 'place' => 'main', 'name' => 'Скрытая ссылка', 'link' => '/h/', 'hidden' => 1],
                7 => ['menu_id' => 7, 'menu_pid' => 2, 'place' => 'main', 'name' => 'Контакты', 'link' => '/contacts/'],
            ],
            4 => [
                8 => ['menu_id' => 8, 'menu_pid' => 4, 'place' => 'main', 'name' => 'Не видна', 'link' => '/x/'],
            ],
        ]);

        $columns = Footer::getMenuColumns('/faq/');

        $this->assertCount(1, $columns);
        $this->assertSame('Информация', $columns[0]['title']);
        $this->assertSame(['FAQ', 'Контакты'], array_column($columns[0]['links'], 'text'));
        $this->assertSame([true, false], array_column($columns[0]['links'], 'active'));
    }
}
