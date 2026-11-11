<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Console\Cli;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\ConsoleBase;
use Simflex\Core\System\Container;

class ConsoleTest extends Unit
{
    public function testFindJobsCountReturnsInt(): void
    {
        $method = new \ReflectionMethod(ConsoleBase::class, 'findJobsCount');
        $this->assertSame(0, $method->invoke(null, 'no_such_job_' . uniqid()));
    }

    /**
     * Исключение команды: код завершения FAILURE, сообщение в stderr; успешная команда - SUCCESS
     */
    public function testCommandExceptionGivesFailureAndStderr(): void
    {
        StderrCli::$lines = [];
        $this->assertSame(Cli::FAILURE, $this->runCommand('fail'));
        $this->assertSame(['Unable to execute. Error: probe failed'], StderrCli::$lines);

        StderrCli::$lines = [];
        $this->assertSame(Cli::SUCCESS, $this->runCommand('ok'));
        $this->assertSame([], StderrCli::$lines);
        $this->assertSame(Cli::FAILURE, $this->runCommand('needsArgument'));
        $this->assertSame(['No argument provided for parameter "name"'], StderrCli::$lines);
    }

    /**
     * ./sf передаёт код завершения процессу: неизвестная команда - не 0, сообщение - в stderr
     */
    public function testSfExitsWithFailureOnUnknownCommand(): void
    {
        $sf = escapeshellarg(Container::getPaths()->getRoot('sf'));
        exec(PHP_BINARY . " $sf no_such_provider/run 2>&1 >/dev/null", $stderr, $code);
        $this->assertSame(Cli::FAILURE, $code);
        $this->assertStringContainsString('Command provider no_such_provider not found', implode("\n", $stderr));

        exec(PHP_BINARY . " $sf help > /dev/null 2>&1", $output, $code);
        $this->assertSame(Cli::SUCCESS, $code);
    }

    private function runCommand(string $command): int
    {
        $cli = new StderrCli(['probe', $command]);
        $this->assertTrue($cli->hasProvider() && $cli->tryLoadClass() && $cli->tryLoadMethod());
        return $cli->execute([]);
    }
}

/**
 * Cli с одним провайдером probe, который собирает строки stderr
 */
class StderrCli extends Cli
{
    /** @var list<string> */
    public static array $lines = [];

    public function __construct(array $cmd)
    {
        $this->providers = ['probe' => ProbeConsole::class];
        [$this->provider, $this->command] = $cmd;
    }

    protected static function writeStderr(string $line): void
    {
        static::$lines[] = $line;
    }
}

class ProbeConsole extends ConsoleBase
{
    #[Command('Throws')]
    public function fail(): void
    {
        throw new \RuntimeException('probe failed');
    }

    #[Command('Succeeds')]
    public function ok(): void
    {
    }

    #[Command('Requires an argument')]
    public function needsArgument(string $name): void
    {
    }
}
