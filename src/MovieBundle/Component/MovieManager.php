<?php

namespace MovieBundle\Component;



use Doctrine\ORM\EntityManagerInterface;
use MovieBundle\Entity\Actor;
use MovieBundle\Entity\Movie;

class MovieManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \MovieBundle\Repository\MovieRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Movie::class);
    }

    /**
     * @param Actor $actor
     * @return array|\MovieBundle\Entity\Movie[]
     */
    public function findMoviesByActor(Actor $actor)
    {
        return $this->repository->findMoviesByActor($actor);
    }
}
