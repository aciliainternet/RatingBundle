<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle;

use Acilia\Bundle\RatingBundle\DependencyInjection\RatingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Rating Bundle
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Alejandro Glejberman <alejandro@acilia.es>
 */
class AciliaRatingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RatingCompilerPass());
    }
}
