<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating\Strategy;

use Acilia\Bundle\RatingBundle\Entity\RatingResult;
use Acilia\Bundle\RatingBundle\Entity\RatingVote;
use Acilia\Bundle\RatingBundle\Library\Rating\VoterInterface;


interface StrategyInterface
{
    /**
     * Returns the name of the strategy
     *
     * @return string
     */
    public function getName();

    /**
     * Calculates the new value of the RatingResult entity.
     *
     * @param RatingResult $result
     * @param RatingVote $vote
     */
    public function addVote(RatingResult $result, RatingVote $vote);

    /**
     * Calculates the new value of the RatingResult entity on update a vote.
     *
     * @param RatingResult $result
     * @param RatingVote $vote
     * @param int $voteValue
     */
    public function updateVote(RatingResult $result, RatingVote $vote, $voteValue);
    /**
     * Calculates the new value of the RatingResult entity on deletes a vote.
     *
     * @param RatingResult $result
     * @param RatingVote $vote
     */
    public function removeVote(RatingResult $result, RatingVote $vote);
}