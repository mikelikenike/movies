<?php

namespace MovieBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use MovieBundle\Entity\Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MovieController extends FOSRestController
{
    /**
     * @Get("/movie/{id}")
     * @ParamConverter("movie", class="MovieBundle:Movie")
     *
     * @param Movie $movie
     * @return Response
     */
    public function getMovieAction(Movie $movie)
    {
        return new Response($this->get('jms_serializer')->serialize($movie, 'json',
            SerializationContext::create()->setGroups(['api'])->enableMaxDepthChecks()),
            Response::HTTP_OK, ['Content-type' => 'application/json']
        );
    }

    /**
     * @Post("/movie/{id}/rating")
     * @ParamConverter("movie", class="MovieBundle:Movie")
     *
     * @param Movie $movie
     * @param Request $request
     * @throws NonUniqueResultException
     * @return Response
     */
    public function postRatingAction(Movie $movie, Request $request)
    {
        try {
            $ratingManager = $this->get('movie.rating_manager');
            $user = $this->getUser();
            if ($ratingManager->findRating($movie, $user)) {
                throw new NonUniqueResultException();
            }
            $rating = $ratingManager->deserializeRating($movie, $user, $request->getContent());
            $violations = $ratingManager->validateRating($rating);
            if ($violations->count()) {
                return $this->handleErrors($violations);
            }
            $ratingManager->saveRating($rating);
            return new Response($ratingManager->serializeRating($rating, ['api']), Response::HTTP_CREATED,
                ['Content-type' => 'application/json']);
        } catch (NonUniqueResultException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Rating already exists'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param ConstraintViolationListInterface $violations
     * @return Response
     */
    private function handleErrors(ConstraintViolationListInterface $violations)
    {
        $errors = [];
        /** @var \Symfony\Component\Validator\ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[] = [
                'status' => 'error',
                'message' => sprintf('Error for %s field: %s', $violation->getPropertyPath(), $violation->getMessage()),
            ];
        }

        return new Response($this->get('serializer')->serialize($errors, 'json'), Response::HTTP_UNPROCESSABLE_ENTITY,
            ['Content-type' => 'application/json']);
    }
}
