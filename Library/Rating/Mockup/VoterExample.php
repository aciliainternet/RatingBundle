<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating\Mockup;

use Acilia\Bundle\RatingBundle\Library\Rating\VoterInterface;

class VoterExample implements VoterInterface
{
    public $id;

    public function getId()
    {
        return $this->id;
    }
}
