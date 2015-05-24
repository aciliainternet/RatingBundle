<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler Pass
 *
 * Load strategies into the Service.
 *
 * @author Alejandro Glejberman <alejandro@acilia.es>
 */
class RatingCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
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
