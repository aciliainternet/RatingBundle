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

    public function incVote(RatingResult $result)
    {
        $newVotes = $result->getVotes() + 1;

        // Set new Votes
        $result->setVotes($newVotes);
    }

    public function decVote(RatingResult $result)
    {
        $newVotes = $result->getVotes() - 1;

        // Set new Votes
        $result->setVotes($newVotes);
    }

    public function calculate(RatingResult $result, $voteValue, $voteNumber)
    {
        $newValue = (($result->getValue() * $result->getVotes()) + $voteValue) / $voteNumber;
        $newValue = round($newValue, 2, PHP_ROUND_HALF_UP);

        // Set new Values
        $result->setValue($newValue);
    }
}