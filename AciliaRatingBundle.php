<?php

namespace Acilia\Bundle\RatingBundle;

use Acilia\Bundle\RatingBundle\DependencyInjection\RatingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AciliaRatingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RatingCompilerPass());
    }

}
