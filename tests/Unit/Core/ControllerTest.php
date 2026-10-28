<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Container;
use Simflex\Http\Controller\Action;
use Simflex\Http\ControllerBase;
use Simflex\Http\Html\HtmlRequest;
use Simflex\Http\Html\HtmlResponse;
use Simflex\Http\Routing\Route;

/**
 * Действия контроллера: путь относительно маршрута, извлечение частей пути, HTTP-метод
 */
class ControllerTest extends Unit
{
    protected mixed $request = null;

    protected function _before(): void
    {
        $this->request = Container::isSet('request') ? Container::getRequest() : null;
    }

    protected function _after(): void
    {
        Container::set('request', $this->request);
    }

    /**
     * @return array<string, array{string, string, string, string}> маршрут, метод, путь => результат
     */
    public static function paths(): array
    {
        return [
            'корень маршрута' => ['/shop', 'GET', '/shop', 'root'],
            'корень со слешем' => ['/shop', 'GET', '/shop/', 'root'],
            'точный путь' => ['/shop', 'GET', '/shop/hello', 'hello'],
            'извлечение части пути' => ['/shop', 'GET', '/shop/phone', 'item:phone'],
            'POST-действие' => ['/shop', 'POST', '/shop/phone/buy', 'buy:phone'],
            'GET не подходит POST-действию' => ['/shop', 'GET', '/shop/phone/buy', 'fallback'],
            'контроллер на корне сайта' => ['/', 'GET', '/hello', 'hello'],
            'нет действия' => ['/shop', 'GET', '/shop/a/b/c', 'fallback'],
        ];
    }

    /**
     * @dataProvider paths
     */
    public function testActionIsResolvedByPath(string $base, string $method, string $path, string $expected): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $path;
        try {
            $request = new HtmlRequest();
            $request->route = new Route(TestController::class, $base);
            Container::set('request', $request);

            $controller = new TestController($request, new HtmlResponse());
            $this->assertSame($expected, $controller->execute());
        } finally {
            unset($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        }
    }
}

class TestController extends ControllerBase
{
    #[Action('/')]
    protected function root(): string
    {
        return 'root';
    }

    // точный путь стоит до пути с извлечением: действия проверяются по порядку
    #[Action('/hello')]
    protected function hello(): string
    {
        return 'hello';
    }

    #[Action('/:item')]
    protected function item(string $item): string
    {
        return 'item:' . $item;
    }

    #[Action('/:item/buy', 'post')]
    protected function buy(string $item): string
    {
        return 'buy:' . $item;
    }

    protected function fallback(): string
    {
        return 'fallback';
    }
}
