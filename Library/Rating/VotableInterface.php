<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating;

interface VotableInterface
{
    public function getResourceType();

    public function getResourceId();

    public function setRating($rating);

    public function getRating();
}
