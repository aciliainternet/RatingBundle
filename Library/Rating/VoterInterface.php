<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle\Library\Rating;

/**
 * Voter Interface
 *
 * Entity who can give votes.
 *
 * @author Andrés Montañez <andres@acilia.es>
 */
interface VoterInterface
{
    public function getId();
}
