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

use Exception;

/**
 * Already Voted Exception
 *
 * Exception thrown when a Vote is cast but there is already a previous vote.
 *
 * @author Andrés Montañez <andres@acilia.es>
 */
class AlreadyVotedException extends Exception
{
}
