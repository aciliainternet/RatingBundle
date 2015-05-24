<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle\Library\Rating\Mockup;

use Acilia\Bundle\RatingBundle\Library\Rating\VotableInterface;

/**
 * Mockup Entity of a Votable
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 */
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
