<?php
namespace Acilia\Bundle\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="rating_vote",
 *     options={"collate"="utf8_unicode_ci", "charset"="utf8", "engine"="InnoDB"},
 *     indexes={
 *         @ORM\Index(name="idx_rating_vote_resource", columns={"vote_resource"}),
 *         @ORM\Index(name="idx_rating_vote_user_resource", columns={"vote_voter", "vote_resource"})
 *     }
 * )
 */
class RatingVote
{
    /**
     * @ORM\Column(type="integer", name="vote_id", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="vote_date", type="date")
     */
    private $date;

    /**
     * @ORM\Column(name="vote_voter", type="integer")
     */
    private $voter;

    /**
     * @ORM\ManyToOne(targetEntity="RatingResult")
     * @ORM\JoinColumn(name="vote_result", referencedColumnName="result_id", nullable=false)
     */
    private $result;

    /**
     * @ORM\Column(name="vote_resource", type="string", length=40)
     */
    private $resource;

    /**
     * @ORM\Column(name="vote_value", type="decimal", precision=5, scale=2)
     */
    private $value;

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
     * Set date
     *
     * @param \DateTime $date
     * @return RatingVote
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set voter
     *
     * @param integer $voter
     * @return RatingVote
     */
    public function setVoter($voter)
    {
        $this->voter = $voter;

        return $this;
    }

    /**
     * Get voter
     *
     * @return integer
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * Set resource
     *
     * @param string $resource
     * @return RatingVote
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
     * Set value
     *
     * @param string $value
     * @return RatingVote
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
     * Set result
     *
     * @param \Acilia\Bundle\RatingBundle\Entity\RatingResult $result
     * @return RatingVote
     */
    public function setResult(\Acilia\Bundle\RatingBundle\Entity\RatingResult $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return \Acilia\Bundle\RatingBundle\Entity\RatingResult
     */
    public function getResult()
    {
        return $this->result;
    }
}
