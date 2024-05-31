<?php

declare(strict_types=1);

namespace GrumPHPTest\Unit\Task;

use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use GrumPHP\Task\SymfonyConsole;
use GrumPHP\Task\TaskInterface;
use GrumPHP\Test\Task\AbstractExternalTaskTestCase;

final class SymfonyConsoleTest extends AbstractExternalTaskTestCase
{
    protected function provideTask(): TaskInterface
    {
        return new SymfonyConsole(
            $this->processBuilder->reveal(),
            $this->formatter->reveal()
        );
    }

    public function provideConfigurableOptions(): iterable
    {
        yield 'default' => [
            [],
            [
                'bin' => './bin/console',
                'command' => [],
            ]
        ];

        yield 'single-command' => [
            [
                'command' => ['task:run'],
            ],
            [
                'bin' => './bin/console',
                'command' => [
                    'task:run'
                ],
            ]
        ];

        yield 'array-command' => [
            [
                'command' => ['task:run', '--env', 'dev', '-vvv'],
            ],
            [
                'bin' => './bin/console',
                'command' => [
                    'task:run',
                    '--env',
                    'dev',
                    '-vvv'
                ],
            ]
        ];
    }

    public function provideRunContexts(): iterable
    {
        yield 'run-context' => [
            true,
            $this->mockContext(RunContext::class)
        ];

        yield 'pre-commit-context' => [
            true,
            $this->mockContext(GitPreCommitContext::class)
        ];

        yield 'other' => [
            false,
            $this->mockContext()
        ];
    }

    public function provideFailsOnStuff(): iterable
    {
        yield 'exitCode1' => [
            [
                'command' => ['--version']
            ],
            $this->mockContext(RunContext::class, ['hello.php', 'hello2.php']),
            function() {
                $process = $this->mockProcess(1);
                $this->mockProcessBuilder('./bin/console', $process);
                $this->formatter->format($process)->willReturn('nope');
            },
            'nope'
        ];
    }

    public function providePassesOnStuff(): iterable
    {
        yield 'exitCode0' => [
            [
                'command' => ['--version']
            ],
            $this->mockContext(RunContext::class, ['hello.php', 'hello2.php']),
            function() {
                $this->mockProcessBuilder('./bin/console', $this->mockProcess());
            }
        ];
    }

    public function provideSkipsOnStuff(): iterable
    {
        yield 'no-files' => [
            [],
            $this->mockContext(RunContext::class),
            function() {
            }
        ];
    }

    public function provideExternalTaskRuns(): iterable
    {
        yield 'single-command' => [
            [
                'command' => ['lint:container']
            ],
            $this->mockContext(RunContext::class, ['hello.php', 'hello2.php']),
            './bin/console',
            [
                'lint:container',
            ]
        ];

        yield 'array-command' => [
            [
                'command' => [
                    'task:run',
                    '--env',
                    'dev',
                    '-vvv'
                ]
            ],
            $this->mockContext(RunContext::class, ['hello.php', 'hello2.php']),
            './bin/console',
            [
                'task:run',
                '--env',
                'dev',
                '-vvv'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider provideFailsNonBlockingOnStuff
     */
    public function it_fails_non_blocking_on_stuff(
        array $config,
        ContextInterface $context,
        callable $configurator,
        string $expectedErrorMessage,
    ): void {
        $task = $this->configureTask($config);
        \Closure::bind($configurator, $this)($task->getConfig()->getOptions(), $context);
        $result = $task->run($context);

        self::assertInstanceOf(TaskResultInterface::class, $result);
        self::assertSame(TaskResultInterface::NONBLOCKING_FAILED, $result->getResultCode());
        self::assertSame($task, $result->getTask());
        self::assertSame($context, $result->getContext());
        self::assertSame($expectedErrorMessage, $result->getMessage());
    }

    public function provideFailsNonBlockingOnStuff(): iterable
    {
        yield 'no-command' => [
            [
                // missing command
            ],
            $this->mockContext(RunContext::class, ['hello.php', 'hello2.php']),
            function() {},
            'Missing "command" configuration for task "symfony_console".'
        ];

        yield 'missing-command-data' => [
            [
                'command' => [], // missing command config
            ],
            $this->mockContext(RunContext::class, ['hello.php', 'hello2.php']),
            function() {},
            'Missing "command" configuration for task "symfony_console".'
        ];
    }
}
