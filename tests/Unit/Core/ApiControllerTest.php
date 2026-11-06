<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Http\Api\ApiController;
use Simflex\Core\Http\Api\ApiException;
use Simflex\Core\Http\Api\JsonEnvelopeResponse;
use Simflex\Core\Http\Api\JsonRequest;
use Simflex\Core\Http\Controller\Action;
use Simflex\Core\Http\Csrf;
use Simflex\Core\Http\Routing\Route;
use Simflex\Core\System\Container;
use Simflex\Core\System\Session;

/**
 * ApiController: подбор действия, параметры из пути, JSON и запроса, ошибки
 */
class ApiControllerTest extends Unit
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
     * @return array{int, array}
     */
    private function call(JsonRequest $request): array
    {
        $request->route = new Route(TestApiController::class, '/api');
        Container::set('request', $request);

        $response = (new TestApiController($request, new JsonEnvelopeResponse()))->handle();
        $status = (new \ReflectionProperty($response, 'statusCode'))->getValue($response);

        return [$status, json_decode($response->getBody(), true)];
    }

    public function testListAction(): void
    {
        [$status, $body] = $this->call(JsonRequest::create('/api/items?limit=2'));

        $this->assertSame(200, $status);
        $this->assertSame([['id' => 1], ['id' => 2]], $body['items']);
        $this->assertSame(0, $body['error']['code']);
    }

    public function testPathVariableAndJsonBody(): void
    {
        [$status, $body] = $this->call(JsonRequest::createJson('/api/items/5', ['title' => 'Ёлка', 'price' => '12.5']));

        $this->assertSame(200, $status);
        $this->assertSame(['id' => 5, 'title' => 'Ёлка', 'price' => 12.5], array_diff_key($body, ['error' => 1]));
    }

    public function testPostParametersFromRequest(): void
    {
        // Codeception уже вывел заголовок в CLI: подавляем только предупреждения PHP о сессии
        set_error_handler(static fn(int $level, string $message) => str_starts_with($message, 'session_'));
        $savedToken = Session::get(Csrf::SESSION_KEY);
        try {
            $post = ['title' => 'Берёза', 'price' => 3, Csrf::FIELD => Csrf::token()];
            [$status, $body] = $this->call(JsonRequest::create('/api/items/6', post: $post));

            $this->assertSame(200, $status);
            $this->assertEquals(['id' => 6, 'title' => 'Берёза', 'price' => 3.0], array_diff_key($body, ['error' => 1]));
        } finally {
            Session::set(Csrf::SESSION_KEY, $savedToken);
            restore_error_handler();
        }
    }

    public function testFormPostWithoutCsrfTokenIsForbidden(): void
    {
        [$status, $body] = $this->call(JsonRequest::create('/api/items/6', post: ['title' => 'Берёза', 'price' => 3]));

        $this->assertSame(403, $status);
        $this->assertSame(403, $body['error']['code']);
        $this->assertArrayNotHasKey('id', $body);
    }

    public function testTypeCasting(): void
    {
        [, $body] = $this->call(JsonRequest::createJson('/api/cast', ['flag' => 'true', 'ids' => [1, 2], 'n' => '8']));

        $this->assertSame(['flag' => true, 'ids' => [1, 2], 'n' => 8, 'tail' => 'x'], array_diff_key($body, ['error' => 1]));
    }

    public function testInvalidTypeIsBadRequest(): void
    {
        [$status, $body] = $this->call(JsonRequest::createJson('/api/cast', ['flag' => 'true', 'ids' => [], 'n' => 'abc']));

        $this->assertSame(400, $status);
        $this->assertSame('Параметр n неверного типа', $body['error']['message']);
    }

    public function testMissingParameterIsBadRequest(): void
    {
        [$status, $body] = $this->call(JsonRequest::createJson('/api/items/5', ['price' => 1]));

        $this->assertSame(400, $status);
        $this->assertSame('Не задан параметр title', $body['error']['message']);
    }

    public function testApiException(): void
    {
        [$status, $body] = $this->call(JsonRequest::create('/api/missing-item'));
        $this->assertSame(404, $status);
        $this->assertSame('Нет такого', $body['error']['message']);

        [$status, $body] = $this->call(JsonRequest::create('/api/invalid'));
        $this->assertSame(422, $status);
        $this->assertSame(['name' => 'Пусто'], $body['error']['errors']);
    }

    public function testUnknownPathIsNotFound(): void
    {
        [$status, $body] = $this->call(JsonRequest::create('/api/nothing/here'));

        $this->assertSame(404, $status);
        $this->assertSame('Метод API не найден', $body['error']['message']);
    }

    public function testHttpMethodMismatchIsNotFound(): void
    {
        [$status] = $this->call(JsonRequest::createJson('/api/items', [], 'DELETE'));
        $this->assertSame(404, $status);
    }

    public function testExceptionIsInternalError(): void
    {
        [$status, $body] = $this->call(JsonRequest::create('/api/boom'));

        $this->assertSame(500, $status);
        $this->assertStringNotContainsString('секрет', json_encode($body, JSON_UNESCAPED_UNICODE));
    }

    public function testRequestParameterIsInjected(): void
    {
        [, $body] = $this->call(JsonRequest::create('/api/path'));
        $this->assertSame('/api/path', $body['result']);
    }
}

class TestApiController extends ApiController
{
    #[Action('/items', 'get')]
    protected function list(int $limit = 10): array
    {
        return array_map(fn(int $id) => ['id' => $id], range(1, $limit));
    }

    #[Action('/items/:id', 'post')]
    protected function save(int $id, string $title, float $price): array
    {
        return ['id' => $id, 'title' => $title, 'price' => $price];
    }

    #[Action('/cast')]
    protected function castAll(bool $flag, array $ids, int $n, string $tail = 'x'): array
    {
        return ['flag' => $flag, 'ids' => $ids, 'n' => $n, 'tail' => $tail];
    }

    #[Action('/missing-item')]
    protected function missing(): never
    {
        throw ApiException::notFound('Нет такого');
    }

    #[Action('/invalid')]
    protected function invalid(): never
    {
        throw ApiException::unprocessable('Ошибка', ['name' => 'Пусто']);
    }

    #[Action('/boom')]
    protected function boom(): never
    {
        throw new \RuntimeException('секрет');
    }

    #[Action('/path')]
    protected function path(JsonRequest $request): string
    {
        return $request->getPath();
    }
}
