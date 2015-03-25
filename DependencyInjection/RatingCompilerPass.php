<?php

namespace Acilia\Bundle\RatingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RatingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('acilia.rating.strategy')) {
            return;
        }

        $factory = $container->getDefinition('acilia.rating.strategy');
        $taggedServices = $container->findTaggedServiceIds('rating.strategy');

        foreach ($taggedServices as $id => $tags) {
            $factory->addMethodCall('addStrategy', [new Reference($id)]);
        }
    }
}
