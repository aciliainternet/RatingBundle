<?php
namespace Acilia\Bundle\RatingBundle\Service;

use Acilia\Bundle\RatingBundle\Library\Rating\Strategy\StrategyInterface;

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