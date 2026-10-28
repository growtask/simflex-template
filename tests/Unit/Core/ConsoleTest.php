<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Console\ConsoleBase;

class ConsoleTest extends Unit
{
    public function testFindJobsCountReturnsInt(): void
    {
        $method = new \ReflectionMethod(ConsoleBase::class, 'findJobsCount');
        $this->assertSame(0, $method->invoke(null, 'no_such_job_' . uniqid()));
    }
}
