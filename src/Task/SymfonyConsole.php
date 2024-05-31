<?php

declare(strict_types=1);

namespace GrumPHP\Task;

use GrumPHP\Formatter\ProcessFormatterInterface;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\Config\ConfigOptionsResolver;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractExternalTask<ProcessFormatterInterface> */
class SymfonyConsole extends AbstractExternalTask
{
    public static function getConfigurableOptions(): ConfigOptionsResolver
    {
        return ConfigOptionsResolver::fromOptionsResolver(
            (new OptionsResolver())
                ->setDefaults([
                    'bin' => './bin/console',
                    'command' => [],
                ])
                ->addAllowedTypes('command', ['string[]'])
                ->setRequired('command')
        );
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return ($context instanceof GitPreCommitContext || $context instanceof RunContext);
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();
        if (0 === \count($context->getFiles())) {
            return TaskResult::createSkipped($this, $context);
        }

        if (0 === \count($config['command'])) {
            return TaskResult::createNonBlockingFailed(
                $this,
                $context,
                'Missing "command" configuration for task "symfony_console".'
            );
        }

        $arguments = $this->processBuilder->createArgumentsForCommand($config['bin']);
        $arguments->addArgumentArray('%s', $config['command']);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            return TaskResult::createFailed($this, $context, $this->formatter->format($process));
        }

        return TaskResult::createPassed($this, $context);
    }
}
