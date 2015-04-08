<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating\Mockup;

use Acilia\Bundle\RatingBundle\Library\Rating\VotableInterface;

class VotableExample implements VotableInterface
{
	public $id;
	public $name;

    public function getResourceType()
    {
    	return 'mockup';
    }

    public function getResourceId()
    {
    	return $this->id;
    }
}