<?php declare(strict_types=1);

namespace Space48\GrumPHPStylelintTask;

use GrumPHP\Extension\ExtensionInterface;
use Space48\GrumPHPStylelintTask\Task\Stylelint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Extension implements ExtensionInterface
{
    public function load(ContainerBuilder $container): void
    {
        $container
            ->register('task.stylelint', Stylelint::class)
            ->addArgument(new Reference('process_builder'))
            ->addArgument(new Reference('formatter.raw_process'))
            ->addTag('grumphp.task', ['task' => 'stylelint']);
    }
}
