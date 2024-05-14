<?php

declare(strict_types=1);

namespace GrumPHPTest\Unit\Task;

use GrumPHP\Runner\FixableTaskResult;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use GrumPHP\Task\TaskInterface;
use GrumPHP\Task\TwigCsFixer;
use GrumPHP\Test\Task\AbstractExternalTaskTestCase;

class TwigCsFixerTest extends AbstractExternalTaskTestCase
{
    protected function provideTask(): TaskInterface
    {
        return new TwigCsFixer(
            $this->processBuilder->reveal(),
            $this->formatter->reveal()
        );
    }

    public function provideConfigurableOptions(): iterable
    {
        yield 'defaults' => [
            [],
            [
                'triggered_by' => ['twig'],
                'path' => '.',
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
            [],
            $this->mockContext(RunContext::class, ['hello.twig']),
            function () {
                $this->mockProcessBuilder('twig-cs-fixer', $process = $this->mockProcess(1));
                $this->formatter->format($process)->willReturn('nope');
            },
            'nope',
            FixableTaskResult::class
        ];
    }

    public function providePassesOnStuff(): iterable
    {
        yield 'exitCode0' => [
            [],
            $this->mockContext(RunContext::class, ['hello.twig']),
            function () {
                $this->mockProcessBuilder('twig-cs-fixer', $this->mockProcess(0));
            }
        ];
    }

    public function provideSkipsOnStuff(): iterable
    {
        yield 'no-files' => [
            [],
            $this->mockContext(RunContext::class),
            function () {}
        ];
        yield 'no-files-after-triggered-by' => [
            [],
            $this->mockContext(RunContext::class, ['notatwigfile.php']),
            function () {}
        ];
    }

    public function provideExternalTaskRuns(): iterable
    {
        yield 'defaults' => [
            [],
            $this->mockContext(RunContext::class, ['hello.twig', 'hello2.twig']),
            'twig-cs-fixer',
            [
                'lint',
                '--report=text',
                '.',
            ]
        ];

        yield 'path' => [
            [
                'path' => 'src',
            ],
            $this->mockContext(RunContext::class, ['hello.twig', 'hello2.twig']),
            'twig-cs-fixer',
            [
                'lint',
                '--report=text',
                'src',
            ]
        ];

        yield 'precommit' => [
            [
                'path' => 'src',
            ],
            $this->mockContext(GitPreCommitContext::class, ['hello.twig', 'hello2.twig']),
            'twig-cs-fixer',
            [
                'lint',
                '--report=text',
                'hello.twig',
                'hello2.twig',
            ]
        ];
    }
}
