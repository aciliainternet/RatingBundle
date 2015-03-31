<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating\Strategy;

use Acilia\Bundle\RatingBundle\Entity\RatingResult;

interface StrategyInterface
{
    /**
     * Returns the name of the strategy
     *
     * @return string
     */
    public function getName();

    /**
     * Increases the amount of votes of the RatingResult entity.
     *
     * @param RatingResult $result
     */
    public function incVote(RatingResult $result);

    /**
     * Decreases the amount of votes of the RatingResult entity.
     *
     * @param RatingResult $result
     */
    public function decVote(RatingResult $result);

    /**
     * Calculates the new value of the RatingResult entity.
     *
     * @param RatingResult $result
     * @param int $voteValue
     * @param int $voteAmount
     */
    public function calculate(RatingResult $result, $voteValue, $voteAmount);
}
