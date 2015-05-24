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

use Acilia\Bundle\RatingBundle\Library\Rating\VoterInterface;

/**
 * Mockup Entity of a Voter
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 */
class VoterExample implements VoterInterface
{
    public $id;

    public function getId()
    {
        return $this->id;
    }
}
