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
        $newVotes = $result->getVotes() + 1;

        // Set new Votes
        $result->setVotes($newVotes);
    }

    public function calculate(RatingResult $result, $voteValue)
    {
        $newValue = (($result->getValue() * $result->getVotes()) + $voteValue) / $result->getVotes();
        $newValue = round($newValue, 2, PHP_ROUND_HALF_UP);

        // Set new Values
        $result->setValue($newValue);
    }
}