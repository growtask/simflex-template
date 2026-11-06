<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Http\Api\ApiException;
use Simflex\Core\Http\Api\JsonEnvelopeResponse;
use Simflex\Core\System\Errors\ErrorCodes;

/**
 * JsonEnvelopeResponse: конверт {"error": {code, message}, ...данные}
 */
class JsonEnvelopeResponseTest extends Unit
{
    private function decode(JsonEnvelopeResponse $response): array
    {
        return json_decode($response->getBody(), true);
    }

    private function statusOf(JsonEnvelopeResponse $response): int
    {
        return (new \ReflectionProperty($response, 'statusCode'))->getValue($response);
    }

    public function testSuccessWithAssociativeData(): void
    {
        $response = (new JsonEnvelopeResponse())->setData(['id' => 7, 'name' => 'Ёлка']);

        $this->assertSame(['error' => ['code' => 0, 'message' => ''], 'id' => 7, 'name' => 'Ёлка'], $this->decode($response));
        $this->assertSame(200, $this->statusOf($response));
        $this->assertStringContainsString('Ёлка', $response->getBody());
    }

    public function testEmptyData(): void
    {
        $response = (new JsonEnvelopeResponse())->setData([]);
        $this->assertSame(['error' => ['code' => 0, 'message' => '']], $this->decode($response));
    }

    public function testListGoesToItems(): void
    {
        $response = (new JsonEnvelopeResponse())->setData([['id' => 1], ['id' => 2]]);
        $this->assertSame([['id' => 1], ['id' => 2]], $this->decode($response)['items']);
    }

    public function testScalarGoesToResult(): void
    {
        $this->assertSame(5, $this->decode((new JsonEnvelopeResponse())->setData(5))['result']);
        $this->assertNull($this->decode((new JsonEnvelopeResponse())->setData(null))['result']);
        $this->assertTrue($this->decode((new JsonEnvelopeResponse())->setData(true))['result']);
    }

    public function testToArrayAndJsonSerializable(): void
    {
        $model = new class () {
            public function toArray(): array
            {
                return ['b' => 2];
            }
        };
        $serializable = new class () implements \JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['a' => 1];
            }
        };

        $this->assertSame(2, $this->decode((new JsonEnvelopeResponse())->setData($model))['b']);
        $this->assertSame(1, $this->decode((new JsonEnvelopeResponse())->setData($serializable))['a']);
    }

    public function testSet(): void
    {
        $response = (new JsonEnvelopeResponse())->set('total', 3);
        $this->assertSame(3, $this->decode($response)['total']);
    }

    public function testErrorKeyInDataIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new JsonEnvelopeResponse())->setData(['error' => 'x']);
    }

    public function testErrorKeyInSetIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new JsonEnvelopeResponse())->set('error', 'x');
    }

    public function testApiException(): void
    {
        $response = (new JsonEnvelopeResponse())->setError(ApiException::notFound('Нет товара'));

        $this->assertSame(404, $this->statusOf($response));
        $this->assertSame(['code' => 404, 'message' => 'Нет товара'], $this->decode($response)['error']);
    }

    public function testApiExceptionWithCodeAndFieldErrors(): void
    {
        $exception = ApiException::unprocessable('Проверьте поля', ['email' => 'Неверный e-mail']);
        $response = (new JsonEnvelopeResponse())->setData($exception);

        $this->assertSame(422, $this->statusOf($response));
        $this->assertSame(
            ['code' => 422, 'message' => 'Проверьте поля', 'errors' => ['email' => 'Неверный e-mail']],
            $this->decode($response)['error']
        );

        $custom = (new JsonEnvelopeResponse())->setError(new ApiException('Занято', 409, 2001));
        $this->assertSame(409, $this->statusOf($custom));
        $this->assertSame(2001, $this->decode($custom)['error']['code']);
    }

    public function testRegularExceptionDoesNotLeakMessage(): void
    {
        $response = (new JsonEnvelopeResponse())->setError(new \RuntimeException('SQL password=secret'));

        $this->assertSame(500, $this->statusOf($response));
        $this->assertStringNotContainsString('secret', $response->getBody());
        $this->assertSame(ErrorCodes::APP_INTERNAL_ERROR, $this->decode($response)['error']['code']);
        $this->assertSame('Внутренняя ошибка, повторите позже', $this->decode($response)['error']['message']);
    }

    public function testExplicitStatusIsKept(): void
    {
        $response = (new JsonEnvelopeResponse())->setStatusCode(409)->setError(ApiException::notFound());
        $this->assertSame(409, $this->statusOf($response));
    }

    public function testSetErrorMessage(): void
    {
        $response = (new JsonEnvelopeResponse())->setErrorMessage('Плохо', 403);
        $this->assertSame(403, $this->statusOf($response));
        $this->assertSame(['code' => 403, 'message' => 'Плохо'], $this->decode($response)['error']);

        $custom = (new JsonEnvelopeResponse())->setErrorMessage('Плохо', 400, 77);
        $this->assertSame(77, $this->decode($custom)['error']['code']);
    }
}
