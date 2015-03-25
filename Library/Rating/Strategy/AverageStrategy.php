<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating\Strategy;

use Acilia\Bundle\RatingBundle\Entity\RatingResult;

class AverageStrategy implements StrategyInterface
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'average';
    }

    public function votes(RatingResult $result)
    {
        $votes = $result->getVotes() + 1;

        return $votes;
    }

    public function calculate(RatingResult $result, $voteValue)
    {
        $newVotes = $this->votes($result);

        $newValue = (($result->getValue() * $result->getVotes()) + $voteValue) / $newVotes;
        $newValue = round($newValue, 2, PHP_ROUND_HALF_UP);

        return $newValue;
    }
}