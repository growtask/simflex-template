<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\System\DI\DIException;
use Simflex\Core\System\DI\Injector;

/**
 * Подбор зависимостей конструктора: параметр со значением по умолчанию, который нельзя
 * взять из контейнера, получает своё значение по умолчанию
 */
class InjectorTest extends Unit
{
    public function testOptionalNonServiceDependencyKeepsDefault(): void
    {
        $args = Injector::resolveClass(InjectorTestConsumer::class);
        $this->assertSame([], $args);
        $consumer = new InjectorTestConsumer(...$args);
        $this->assertInstanceOf(InjectorTestPlain::class, $consumer->plain);
        $this->assertSame(5, $consumer->limit);
    }

    public function testPassedParameterIsUsedByName(): void
    {
        $plain = new InjectorTestPlain();
        $args = Injector::resolveClass(InjectorTestConsumer::class, $plain);
        $this->assertSame(['plain' => $plain], $args);
        $this->assertSame($plain, (new InjectorTestConsumer(...$args))->plain);
    }

    public function testRequiredNonServiceDependencyStillFails(): void
    {
        $this->expectException(DIException::class);
        $this->expectExceptionMessage('does not implement Service interface');
        Injector::resolveClass(InjectorTestRequired::class);
    }
}

class InjectorTestPlain
{
}

class InjectorTestConsumer
{
    public function __construct(
        public InjectorTestPlain $plain = new InjectorTestPlain(),
        public int $limit = 5,
    ) {
    }
}

class InjectorTestRequired
{
    public function __construct(public InjectorTestPlain $plain)
    {
    }
}
