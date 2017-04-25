<?php

namespace MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rating
 *
 * @ORM\Table(name="rating", uniqueConstraints={@ORM\UniqueConstraint(name="rating_unique", columns={"user_id", "movie_id"})})
 * @ORM\Entity(repositoryClass="MovieBundle\Repository\RatingRepository")
 */
class Rating
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="MovieBundle\Entity\User", inversedBy="ratings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    protected $user;

    /**
     * @var Movie
     *
     * @ORM\ManyToOne(targetEntity="MovieBundle\Entity\Movie", inversedBy="ratings")
     * @ORM\JoinColumn(name="movie_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    protected $movie;

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="smallint")
     * @Assert\Range(min=0, max=10)
     */
    protected $rating;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Rating
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set movie
     *
     * @param Movie $movie
     *
     * @return Rating
     */
    public function setMovie(Movie $movie)
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * Get movie
     *
     * @return Movie
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }
}

