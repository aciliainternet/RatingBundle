<?php
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
	}

	protected function validateVoteValue($value)
	{
		$value = (integer) $value;

		if ($value < $this->options['min']) {
			$value = $this->options['min'];

		} else if ($value > $this->options['max']) {
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
		$alreadyVoted = false;
		$resource = RatingResult::calculateResource($votable);

		$key = 'Rating::' . $resource . '::' . $voter->getId();
		$alreadyVoted = $this->memcache->get($key);

		if ($this->memcache->notFound()) {
			$em = $this->doctrine->getManager();
			$vote = $em->getRepository('AciliaRatingBundle:RatingVote')->findOneBy([
		    	'voter' => $voter->getId(),
				'resource' => $resource,
			]);

			if ($vote instanceOf RatingVote) {
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

			// Calculate new Votes and Values
            $this->getStrategy()->votes($result);
            $this->getStrategy()->calculate($result, $voteValue);

			// Create Vote
			$vote = new RatingVote();
			$vote->setDate(new DateTime())
                 ->setResult($result)
                 ->setResource($result->getResource())
                 ->setVoter($voter->getId())
                 ->setValue($voteValue);
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

			if ($vote instanceOf RatingVote) {

                // Calculate updated Values
                $updatedValue = $voteValue - $result->getValue();
				$this->getStrategy()->calculate($result, $updatedValue);

				// Set updated Values
				$vote->setValue($voteValue);
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

			if ($vote instanceOf RatingVote) {
				// Calculate updated Values
				if ($result->getVotes() == 1) {
					$updatedValue = 0;
					$updatedVotes = 0;
				} else {
					$updatedVotes = $result->getVotes() - 1;
					$updatedValue = (($result->getValue() * $result->getVotes()) - $vote->getValue()) / $updatedVotes;
					$updatedValue = round($updatedValue, 2, PHP_ROUND_HALF_UP);
				}

				// Update user vote on Memcache
				$key = 'Rating::' . $result->getResource() . '::' . $voter->getId();
				$this->memcache->set($key, false, 24);

				// Save user vote Value on Memcache
				$key = 'RatingValue::' . $result->getResource() . '::' . $voter->getId();
				$this->memcache->delete($key);

				// Set updated Values
				$result->setValue($updatedValue)->setVotes($updatedVotes);
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
		$value = false;
		$resource = RatingResult::calculateResource($votable);

		// Check if User HAS voted
		if ($this->checkIfVoterAlreadyVoted($votable, $voter)) {
			$key = 'RatingValue::' . $resource . '::' . $voter->getId();
		    $value = $this->memcache->get($key);

		    if ($this->memcache->notFound()) {
		    	$vote = $this->getVote($votable, $voter);
		    	if ($vote instanceOf RatingVote) {
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