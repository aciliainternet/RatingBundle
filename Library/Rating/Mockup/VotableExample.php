<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating\Mockup;

use Acilia\Bundle\RatingBundle\Library\Rating\VotableInterface;

class VotableExample implements VotableInterface
{
	public $id;
	public $name;
    protected $rating;

    public function getResourceType()
    {
    	return 'mockup';
    }

    public function getResourceId()
    {
    	return $this->id;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    public function getRating()
    {
        return $this->rating;
    }
}