<?php

/*
 * This file is part of the Acilia Component / Rating Bundle.
 *
 * (c) Acilia Internet S.L. <info@acilia.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acilia\Bundle\RatingBundle\Entity;

use Acilia\Bundle\RatingBundle\Library\Rating\VotableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Rating Result
 *
 * Entity for storing the Result of a set of votes.
 *
 * @author Andrés Montañez <andres@acilia.es>
 * @author Rodrigo Mendez <rodrigo@acilia.es>
 *
 * @ORM\Entity()
 * @ORM\Table(name="rating_result",
 *     options={"collate"="utf8_unicode_ci", "charset"="utf8", "engine"="InnoDB"},
 *     indexes={
 *         @ORM\Index(name="idx_rating_result_resource_compound", columns={"result_resource_type", "result_resource_id"}),
 *         @ORM\Index(name="idx_rating_result_resource", columns={"result_resource"})
 *     }
 * )
 */
class RatingResult
{
    /**
     * @ORM\Column(type="integer", name="result_id", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="result_resource", type="string", length=40)
     */
    private $resource;

    /**
     * @ORM\Column(name="result_resource_type", type="string", length=32)
     */
    private $resourceType;

    /**
     * @ORM\Column(name="result_resource_id", type="string", length=32)
     */
    private $resourceId;

    /**
     * @ORM\Column(name="result_votes", type="integer")
     */
    private $votes;

    /**
     * @ORM\Column(name="result_value", type="decimal", precision=5, scale=2)
     */
    private $value;

    /**
     * @ORM\Column(name="result_extra", type="string", length=32)
     */
    private $extra;


    public static function calculateResource(VotableInterface $votable)
    {
        $resource = sha1($votable->getResourceType())
                  . '#'
                  . sha1($votable->getResourceId());
        $resource = sha1($resource);

        return $resource;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set resource
     *
     * @param string $resource
     * @return RatingResult
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set resourceType
     *
     * @param string $resourceType
     * @return RatingResult
     */
    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    /**
     * Get resourceType
     *
     * @return string
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Set resourceId
     *
     * @param string $resourceId
     * @return RatingResult
     */
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * Get resourceId
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     * @return RatingResult
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return integer
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return RatingResult
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set extra
     *
     * @param string $extra
     * @return RatingResult
     */
    public function setExtra($value)
    {
        $this->extra = $value;

        return $this;
    }

    /**
     * Get extra
     *
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }
}
