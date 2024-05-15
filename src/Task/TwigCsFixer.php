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
use GrumPHP\Fixer\Provider\FixableProcessResultProvider;
use Symfony\Component\Process\Process;

/**
 * @extends AbstractExternalTask<ProcessFormatterInterface>
 */
class TwigCsFixer extends AbstractExternalTask
{
    public static function getConfigurableOptions(): ConfigOptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'paths' => ['.'],
            'level' => 'NOTICE',
            'config' => null,
            'report' => 'text',
            'fix' => false,
            'no-cache' => false,
            'debug' => false,
            'quiet' => false,
            'version' => false,
            'ansi' => false,
            'no-ansi' => false,
            'no-interaction' => false,
            'verbose' => false,
            'triggered_by' => ['twig'],
        ]);

        $resolver->addAllowedTypes('paths', ['array']);
        $resolver->addAllowedTypes('level', ['string']);
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('report', ['string']);
        $resolver->addAllowedTypes('fix', ['bool']);
        $resolver->addAllowedTypes('no-cache', ['bool']);
        $resolver->addAllowedTypes('debug', ['bool']);
        $resolver->addAllowedTypes('quiet', ['bool']);
        $resolver->addAllowedTypes('version', ['bool']);
        $resolver->addAllowedTypes('ansi', ['bool']);
        $resolver->addAllowedTypes('no-ansi', ['bool']);
        $resolver->addAllowedTypes('no-interaction', ['bool']);
        $resolver->addAllowedTypes('verbose', ['bool']);

        return ConfigOptionsResolver::fromOptionsResolver($resolver);
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();
        $files = $context->getFiles()
            ->extensions($config['triggered_by'])
            ->paths($config['paths']);

        if (\count($files) === 0) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = $this->processBuilder->createArgumentsForCommand('twig-cs-fixer');
        $arguments->add('lint');

        if ($context instanceof GitPreCommitContext) {
            $arguments->addFiles($files);
        }

        if ($context instanceof RunContext) {
            foreach ($config['paths'] as $path) {
                $arguments->add($path);
            }
        }

        $arguments->addOptionalArgument('--level=%s', $config['level']);
        $arguments->addOptionalArgument('--config=%s', $config['config']);
        $arguments->addOptionalArgument('--report=%s', $config['report']);

        $arguments->addOptionalArgument('--fix', $config['fix']);
        $arguments->addOptionalArgument('--no-cache', $config['no-cache']);
        $arguments->addOptionalArgument('--debug', $config['debug']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgument('--version', $config['version']);
        $arguments->addOptionalArgument('--ansi', $config['ansi']);
        $arguments->addOptionalArgument('--no-ansi', $config['no-ansi']);
        $arguments->addOptionalArgument('--no-interaction', $config['no-interaction']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            return FixableProcessResultProvider::provide(
                TaskResult::createFailed($this, $context, $this->formatter->format($process)),
                function () use ($arguments): Process {
                    $arguments->add('--fix');
                    return $this->processBuilder->buildProcess($arguments);
                }
            );
        }

        return TaskResult::createPassed($this, $context);
    }
}
