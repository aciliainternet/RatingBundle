<?php
namespace Acilia\Bundle\RatingBundle\Library\Rating\Strategy;

use Acilia\Bundle\RatingBundle\Entity\RatingResult;


interface StrategyInterface
{
    public function getName();

    public function votes(RatingResult $result);

    public function calculate(RatingResult $result, $voteValue);

}