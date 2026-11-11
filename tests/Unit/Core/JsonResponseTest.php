<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Http\Api\JsonResponse;

/**
 * JsonResponse: тело ответа - ровно переданные данные, без конверта
 */
class JsonResponseTest extends Unit
{
    /**
     * @return array<string, array{mixed, string}>
     */
    public static function data(): array
    {
        return [
            'ассоциативный массив' => [['id' => 1, 'name' => 'Ёлка'], '{"id":1,"name":"Ёлка"}'],
            'список' => [[1, 2, 3], '[1,2,3]'],
            'пустой массив' => [[], '[]'],
            'строка' => ['ok', '"ok"'],
            'число' => [5, '5'],
            'bool' => [true, 'true'],
            'null' => [null, 'null'],
            'JsonSerializable' => [new JsonSerializableStub(), '{"a":1}'],
            'объект с toArray' => [new ToArrayStub(), '{"b":2}'],
        ];
    }

    /**
     * @dataProvider data
     */
    public function testBodyIsExactlyData(mixed $data, string $expected): void
    {
        $this->assertSame($expected, (new JsonResponse())->setData($data)->getBody());
    }

    public function testUnsupportedObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new JsonResponse())->setData(new \stdClass());
    }

    public function testContentTypeHeader(): void
    {
        $response = new JsonResponse();
        $headers = new \ReflectionProperty($response, 'headers');
        $this->assertSame('application/json; charset=utf-8', $headers->getValue($response)['Content-Type']);
    }
}

class JsonSerializableStub implements \JsonSerializable
{
    public function jsonSerialize(): array
    {
        return ['a' => 1];
    }
}

class ToArrayStub
{
    public function toArray(): array
    {
        return ['b' => 2];
    }
}
