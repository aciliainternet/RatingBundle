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

use Acilia\Bundle\RatingBundle\Event\RatedEvent;
use Acilia\Component\Memcached\Service\MemcachedService;
use Acilia\Bundle\RatingBundle\Entity\RatingResult;
use Acilia\Bundle\RatingBundle\Entity\RatingVote;
use Acilia\Bundle\RatingBundle\Library\Rating\VoterInterface;
use Acilia\Bundle\RatingBundle\Library\Rating\VotableInterface;
use Acilia\Bundle\RatingBundle\Library\Rating\AlreadyVotedException;
use Acilia\Bundle\RatingBundle\Library\Rating\NotVotedException;
use Acilia\Bundle\RatingBundle\Service\RatingStrategyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use DateTime;

/**
 * Rating Service
 *
 * Service for handling the Votes
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Alejandro Glejberman <alejandro@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 */
class RatingService
{
    /**
     * Event Triggered when the something is Rated
     * @var string
     */
    const EVENT_RATED = 'votable.rated';

    protected $doctrine;
    protected $memcache;
    protected $options;
    protected $strategy;

    /**
     * Event Dipatcher
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct($doctrine, MemcachedService $memcache, $options, EventDispatcherInterface $eventDispatcher, RatingStrategyService $strategy)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->doctrine = $doctrine;
        $this->memcache = $memcache;
        $this->options = $options;
        $this->strategy = $strategy;

        if (!isset($this->options['min']) || !is_numeric($this->options['min'])) {
            $this->options['min'] = 1;
        }

        if (!isset($this->options['max']) || !is_numeric($this->options['max'])) {
            $this->options['max'] = 5;
        }

        if (!isset($this->options['strategy'])) {
            $this->options['strategy'] = 'average';
        }
    }

    protected function validateVoteValue($value)
    {
        // Ensure value is a 2 digit number, don't round it, just trim extra digits.
        $value = (float) $value * 100;
        $value = (integer) $value;
        $value = $value / 100;

        if ($value < $this->options['min']) {
            $value = $this->options['min'];
        } elseif ($value > $this->options['max']) {
            $value = $this->options['max'];
        }

        return $value;
    }

    protected function getResult(VotableInterface $votable, $create = true)
    {
        $em = $this->doctrine->getManager();
        $resource = RatingResult::calculateResource($votable);

        $result = $em->getRepository('AciliaRatingBundle:RatingResult')->findOneByResource($resource);
        if (!$result && $create == true) {
            $result = new RatingResult();
            $result->setResource($resource)
                   ->setResourceType($votable->getResourceType())
                   ->setResourceId($votable->getResourceId())
                   ->setValue(0)
                   ->setVotes(0);
            $em->persist($result);
        }

        return $result;
    }

    protected function getVote(VotableInterface $votable, VoterInterface $voter)
    {
        $em = $this->doctrine->getManager();
        $resource = RatingResult::calculateResource($votable);

        $vote = $em->getRepository('AciliaRatingBundle:RatingVote')->findOneBy([
            'voter' => $voter->getId(),
            'resource' => $resource,
        ]);

        return $vote;
    }

    protected function checkIfVoterAlreadyVoted(VotableInterface $votable, VoterInterface $voter)
    {
        $resource = RatingResult::calculateResource($votable);

        $key = 'Rating::' . $resource . '::' . $voter->getId();
        $alreadyVoted = $this->memcache->get($key);

        if ($this->memcache->notFound()) {
            $em = $this->doctrine->getManager();
            $vote = $em->getRepository('AciliaRatingBundle:RatingVote')->findOneBy([
                'voter' => $voter->getId(),
                'resource' => $resource,
            ]);

            if ($vote instanceof RatingVote) {
                $alreadyVoted = true;
            }

            $this->memcache->set($key, $alreadyVoted, 24);
        }

        return $alreadyVoted;
    }

    public function vote(VotableInterface $votable, VoterInterface $voter, $voteValue)
    {
        $voteValue = $this->validateVoteValue($voteValue);

        // Check if User has NOT voted already
        if (!$this->checkIfVoterAlreadyVoted($votable, $voter)) {
            $em = $this->doctrine->getManager();

            // Get Result
            $result = $this->getResult($votable);

            $vote = new RatingVote();
            $vote->setDate(new DateTime())
                ->setResult($result)
                ->setResource($result->getResource())
                ->setVoter($voter->getId())
                ->setValue($voteValue);

            // Calculate new Votes and Values
            $this->getStrategy()->addVote($result, $vote);

            // Set new Values
            $em->persist($result);
            $em->persist($vote);
            $em->flush();

            // Save user vote on Memcache
            $key = 'Rating::' . $result->getResource() . '::' . $voter->getId();
            $this->memcache->set($key, true, 24);

            // Save user vote Value on Memcache
            $key = 'RatingValue::' . $result->getResource() . '::' . $voter->getId();
            $this->memcache->set($key, $voteValue, 1440);

            // Save vote result on Memcache
            $key = 'Rating::' . $result->getResource();
            $this->memcache->set($key, $result->getValue(), 24);

            $ratingValue = $result->getValue();

            // Dispatch Event
            $event = new RatedEvent($voter, $votable, $voteValue, $ratingValue, RatedEvent::TYPE_VOTE);
            $this->eventDispatcher->dispatch(self::EVENT_RATED, $event);

            return $ratingValue;
        }

        throw new AlreadyVotedException();
    }

    public function updateVote(VotableInterface $votable, VoterInterface $voter, $voteValue)
    {
        $voteValue = $this->validateVoteValue($voteValue);

        // Check if User HAS voted already
        if ($this->checkIfVoterAlreadyVoted($votable, $voter)) {
            $em = $this->doctrine->getManager();

            // Get Result and Vote
            $result = $this->getResult($votable);
            $vote = $this->getVote($votable, $voter);

            if ($vote instanceof RatingVote) {
                // Calculate updated Values
                $this->getStrategy()->updateVote($result, $vote, $voteValue);

                // Set updated Values
                $em->persist($result);
                $em->persist($vote);
                $em->flush();

                // Save user vote Value on Memcache
                $key = 'RatingValue::' . $result->getResource() . '::' . $voter->getId();
                $this->memcache->set($key, $voteValue, 1440);

                // Save vote result on Memcache
                $key = 'Rating::' . $result->getResource();
                $this->memcache->set($key, $result->getValue(), 24);

                $ratingValue = $result->getValue();

                // Dispatch Event
                $event = new RatedEvent($voter, $votable, $voteValue, $ratingValue, RatedEvent::TYPE_UPDATE);
                $this->eventDispatcher->dispatch(self::EVENT_RATED, $event);

                return $ratingValue;
            } else {
                throw new NotVotedException();
            }
        }

        throw new NotVotedException();
    }

    public function removeVote(VotableInterface $votable, VoterInterface $voter)
    {
        // Check if User HAS voted already
        if ($this->checkIfVoterAlreadyVoted($votable, $voter)) {
            $em = $this->doctrine->getManager();

            // Get Result and Vote
            $result = $this->getResult($votable);
            $vote = $this->getVote($votable, $voter);

            if ($vote instanceof RatingVote) {
                // Calculate updated Values
                $this->getStrategy()->removeVote($result, $vote);

                // Update user vote on Memcache
                $key = 'Rating::' . $result->getResource() . '::' . $voter->getId();
                $this->memcache->set($key, false, 24);

                // Save user vote Value on Memcache
                $key = 'RatingValue::' . $result->getResource() . '::' . $voter->getId();
                $this->memcache->delete($key);

                // Set updated Values
                $em->remove($vote);
                $em->flush();

                // Save vote result on Memcache
                $key = 'Rating::' . $result->getResource();
                $this->memcache->set($key, $result->getValue(), 24);

                $ratingValue = $result->getValue();

                // Dispatch Event
                $event = new RatedEvent($voter, $votable, null, $ratingValue, RatedEvent::TYPE_REMOVE);
                $this->eventDispatcher->dispatch(self::EVENT_RATED, $event);

                return $ratingValue;
            } else {
                throw new NotVotedException();
            }
        }

        throw new NotVotedException();
    }

    public function getVoteResult(VotableInterface $votable)
    {
        $resource = RatingResult::calculateResource($votable);
        $key = 'Rating::' . $resource;

        $value = $this->memcache->get($key);
        if ($this->memcache->notFound()) {
            // Get Result
            $result = $this->getResult($votable, false);

            if ($result) {
                $value =  $result->getValue();
            } else {
                $value = 0;
            }

            // Save result on Memcache
            $this->memcache->set($key, $value, 24);
        }

        return $value;
    }

    public function getVoterVoteResult(VoterInterface $voter, VotableInterface $votable)
    {
        $resource = RatingResult::calculateResource($votable);

        // Check if User HAS voted
        if ($this->checkIfVoterAlreadyVoted($votable, $voter)) {
            $key = 'RatingValue::' . $resource . '::' . $voter->getId();
            $value = $this->memcache->get($key);

            if ($this->memcache->notFound()) {
                $vote = $this->getVote($votable, $voter);
                if ($vote instanceof RatingVote) {
                    $value = $vote->getValue();
                    $this->memcache->set($key, $value, 1440);
                }
            }
        } else {
            throw new NotVotedException();
        }

        return $value;
    }

    protected function getStrategy()
    {
        return $this->strategy->getStrategy($this->options['strategy']);
    }
}
