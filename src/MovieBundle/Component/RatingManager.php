<?php

namespace MovieBundle\Component;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use MovieBundle\Entity\Movie;
use MovieBundle\Entity\Rating;
use MovieBundle\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RatingManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \MovieBundle\Repository\RatingRepository
     */
    protected $repository;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Rating::class);
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param Movie $movie
     * @param User $user
     * @param string $data
     * @return Rating
     */
    public function deserializeRating(Movie $movie, User $user, $data)
    {
        $rating = $this->serializer->deserialize($data, Rating::class, 'json');
        $rating->setMovie($movie);
        $rating->setUser($user);
        return $rating;
    }

    /**
     * @param Movie $movie
     * @param User $user
     * @return Rating|null
     */
    public function findRating(Movie $movie, User $user)
    {
        return $this->repository->findOneBy([
            'movie' => $movie,
            'user' => $user,
        ]);
    }

    /**
     * @param Rating $rating
     * @param bool|true $flush
     */
    public function saveRating(Rating $rating, $flush = true)
    {
        $this->entityManager->persist($rating);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Rating $rating
     * @param array $groups
     * @return string
     */
    public function serializeRating(Rating $rating, array $groups)
    {
        $context = null;
        if (!empty($groups)) {
            $context = SerializationContext::create()->setGroups($groups);
        }
        return $this->serializer->serialize($rating, 'json', $context);
    }

    /**
     * @param Rating $rating
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateRating(Rating $rating)
    {
        return $this->validator->validate($rating);
    }
}
