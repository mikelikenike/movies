<?php

namespace MovieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * Actor
 *
 * @ORM\Table(name="actor", indexes={@ORM\Index(name="name_index", columns={"first_name", "last_name"})})
 * @ORM\Entity(repositoryClass="MovieBundle\Repository\ActorRepository")
 * @JMSSerializer\ExclusionPolicy("all")
 */
class Actor
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
     * @ORM\Column(name="first_name", type="string", length=255)
     * @JMSSerializer\Expose()
     * @JMSSerializer\Groups({"api"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     * @JMSSerializer\Expose()
     * @JMSSerializer\Groups({"api"})
     */
    protected $lastName;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MovieBundle\Entity\Movie", mappedBy="actors")
     */
    protected $movies;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     * @return Actor
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Actor
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set movies
     *
     * @param ArrayCollection $movies
     * @return Actor
     */
    public function setMovies(ArrayCollection $movies)
    {
        $this->movies = $movies;

        return $this;
    }

    /**
     * Get movies
     *
     * @return ArrayCollection
     */
    public function getMovies()
    {
        return $this->movies;
    }
}
