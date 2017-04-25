<?php

namespace MovieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * Movie
 *
 * @ORM\Table(name="movie", indexes={@ORM\Index(name="title_index", columns={"title"})})
 * @ORM\Entity(repositoryClass="MovieBundle\Repository\MovieRepository")
 * @JMSSerializer\ExclusionPolicy("all")
 */
class Movie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @JMSSerializer\Expose()
     * @JMSSerializer\Groups({"api"})
     */
    protected $title;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="smallint")
     * @JMSSerializer\Expose()
     * @JMSSerializer\Groups({"api"})
     */
    protected $year;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MovieBundle\Entity\Actor", inversedBy="movies")
     * @ORM\JoinTable(name="movie_actors")
     * @JMSSerializer\Expose()
     * @JMSSerializer\Groups({"api"})
     * @JMSSerializer\MaxDepth(2)
     */
    protected $actors;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MovieBundle\Entity\Rating", mappedBy="movie")
     */
    protected $ratings;

    public function __construct()
    {
        $this->actors = new ArrayCollection();
        $this->ratings = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Movie
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Movie
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set actors
     *
     * @param ArrayCollection $actors
     * @return Movie
     */
    public function setActors(ArrayCollection $actors)
    {
        $this->actors = $actors;

        return $this;
    }

    /**
     * Get actors
     *
     * @return ArrayCollection
     */
    public function getActors()
    {
        return $this->actors;
    }

    /**
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param ArrayCollection $ratings
     *
     * @return Movie
     */
    public function setRatings(ArrayCollection $ratings)
    {
        $this->ratings = $ratings;

        return $this;
    }
}
