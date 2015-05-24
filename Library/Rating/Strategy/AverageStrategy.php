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
 * Average Strategy
 *
 * Strategy for an Average Calculation of the Rating.
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 * @author Alejandro Glejberman <alejandro@acilia.es>
 */
class AverageStrategy implements StrategyInterface
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'average';
    }

    public function addVote(RatingResult $result, RatingVote $vote)
    {
        $newValue = (($result->getValue() * $result->getVotes()) + $vote->getValue()) / ($result->getVotes() + 1);
        $newValue = round($newValue, 2, PHP_ROUND_HALF_UP);

        // Set new Values
        $result->setValue($newValue);
        $result->setVotes($result->getVotes() + 1);
    }

    public function updateVote(RatingResult $result, RatingVote $vote, $voteValue)
    {
        $newValue = (($result->getValue() * $result->getVotes()) - $vote->getValue() + $voteValue) / $result->getVotes();
        $newValue = round($newValue, 2, PHP_ROUND_HALF_UP);

        // Set new Values
        $vote->setValue($voteValue);
        $result->setValue($newValue);
    }

    public function removeVote(RatingResult $result, RatingVote $vote)
    {
        $newValue = 0;
        if ($result->getVotes() > 1) {
            $newValue = (($result->getValue() * $result->getVotes()) - $vote->getValue()) / ($result->getVotes() - 1);
        }
        $newValue = round($newValue, 2, PHP_ROUND_HALF_UP);

        // Set new Values
        $result->setValue($newValue);
        $result->setVotes($result->getVotes() - 1);
    }
}
