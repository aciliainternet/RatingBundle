<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle\Event;

use Acilia\Bundle\RatingBundle\Library\Rating\VoterInterface;
use Acilia\Bundle\RatingBundle\Library\Rating\VotableInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Rated Event
 *
 * Event dispatched when a Vote Action is triggered
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 */
class RatedEvent extends Event
{
    const TYPE_VOTE = 'vote';
    const TYPE_UPDATE = 'update';
    const TYPE_REMOVE = 'remove';

    protected $voter;
    protected $votable;
    protected $value;
    protected $rating;

    public function __construct(VoterInterface $voter, VotableInterface $votable, $value, $rating, $type)
    {
        $this->voter = $voter;
        $this->votable = $votable;
        $this->value = $value;
        $this->rating = $rating;
        $this->type = $type;
    }

    public function getVoter()
    {
        return $this->voter;
    }

    public function getVotable()
    {
        return $this->votable;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getType()
    {
        return $this->type;
    }
}
