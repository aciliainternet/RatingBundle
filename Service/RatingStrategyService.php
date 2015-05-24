<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle\Service;

use Acilia\Bundle\RatingBundle\Library\Rating\Strategy\StrategyInterface;

/**
 * Strategy Service
 *
 * Service for handling the Rating Strategies
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Alejandro Glejberman <alejandro@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 */
class RatingStrategyService
{
    protected $strategies = [];

    public function __construct()
    {
    }

    public function addStrategy(StrategyInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    public function getStrategy($name)
    {
        if (isset($this->strategies[$name])) {
            return $this->strategies[$name];
        }

        throw new \Exception('Strategy "' . $name . '" is not registered');
    }

    public function hasStrategy($name)
    {
        return isset($this->strategies[$name]);
    }

    public function getRegisteredStrategies()
    {
        return array_keys($this->strategies);
    }
}
