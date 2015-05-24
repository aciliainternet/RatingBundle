<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle\Library\Rating\Strategy;

use Acilia\Bundle\RatingBundle\Entity\RatingResult;
use Acilia\Bundle\RatingBundle\Entity\RatingVote;
use Acilia\Bundle\RatingBundle\Library\Rating\VoterInterface;

/**
 * Strategy Interface
 *
 * All Rating Strategies must implement this Interface.
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 * @author Alejandro Glejberman <alejandro@acilia.es>
 */
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
     * @param float $voteValue
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
