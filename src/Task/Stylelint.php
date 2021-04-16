<?php declare(strict_types=1);

namespace Space48\GrumPHPStylelintTask\Task;

use GrumPHP\Collection\ProcessArgumentsCollection;
use GrumPHP\Fixer\Provider\FixableProcessProvider;
use GrumPHP\Runner\FixableTaskResult;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Stylelint extends AbstractExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            // Task config options
            'bin' => null,
            'triggered_by' => ['css', 'less', 'scss', 'sass', 'pcss'],
            'allowed_paths' => null,

            // stylelint config options
            'config' => null,
            'disable_default_ignores' => false,
            'format' => null,
            'max_warnings' => null,
            'quiet' => false,
        ]);

        // Task config options
        $resolver->addAllowedTypes('bin', ['null', 'string', 'array']);
        $resolver->addAllowedTypes('allowed_paths', ['null', 'array']);
        $resolver->addAllowedTypes('triggered_by', ['array']);

        // stylelint config options
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('disable_default_ignores', ['bool']);
        $resolver->addAllowedTypes('format', ['null', 'string']);
        $resolver->addAllowedTypes('max_warnings', ['null', 'integer']);
        $resolver->addAllowedTypes('quiet', ['bool']);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return ($context instanceof GitPreCommitContext || $context instanceof RunContext);
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();

        $files = $context
            ->getFiles()
            ->paths($config['allowed_paths'] ?? [])
            ->extensions($config['triggered_by']);

        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = isset($config['bin'])
            ? array_reduce(
                is_array($config['bin']) ? $config['bin'] : [$config['bin']],
                static function ($carry, $item): ProcessArgumentsCollection {
                    if ($carry instanceof ProcessArgumentsCollection) {
                        $carry->add($item);
                        return $carry;
                    }

                    return ProcessArgumentsCollection::forExecutable($item);
                }
            )
            : $this->processBuilder->createArgumentsForCommand('stylelint');

        $arguments->addOptionalArgument('--config=%s', $config['config']);
        $arguments->addOptionalArgument('--disable-default-ignores', $config['disable_default_ignores']);
        $arguments->addOptionalIntegerArgument('--formatter=%s', $config['format']);
        $arguments->addOptionalIntegerArgument('--max-warnings=%d', $config['max_warnings']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);

        $arguments->addFiles($files);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            $arguments->add('--fix');
            $fixerCommand = $this->processBuilder
                ->buildProcess($arguments)
                ->getCommandLine();

            $message = sprintf(
                '%sYou can fix errors by running the following command:%s',
                $this->formatter->format($process) . PHP_EOL . PHP_EOL,
                PHP_EOL . $fixerCommand
            );

            return new FixableTaskResult(
                TaskResult::createFailed($this, $context, $message),
                FixableProcessProvider::provide($fixerCommand)
            );
        }

        return TaskResult::createPassed($this, $context);
    }
}
