<?php

namespace MovieBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use MovieBundle\Entity\Actor;
use MovieBundle\Entity\Movie;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MovieController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Get movie data by unique id",
     *     output="MovieBundle\Entity\Movie",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when movie is not found"
     *     }
     * )
     *
     * @Get("/movie/{id}", requirements={"id" = "\d+"})
     * @ParamConverter("movie", class="MovieBundle:Movie")
     *
     * @param Movie $movie
     * @return Response
     */
    public function getMovieAction(Movie $movie)
    {
        return new Response($this->get('jms_serializer')->serialize($movie, 'json',
            SerializationContext::create()->setGroups(['movie'])->enableMaxDepthChecks()),
            Response::HTTP_OK, ['Content-type' => 'application/json']
        );
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Get movies by unique actor id",
     *     output="MovieBundle\Entity\Movie",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when actor is not found"
     *     }
     * )
     *
     * @Get("/actor/{id}/movie", requirements={"id" = "\d+"})
     * @ParamConverter("actor", class="MovieBundle:Actor")
     *
     * @param Actor $actor
     * @return Response
     */
    public function getActorMovies(Actor $actor)
    {
        $movies = $this->get('movie.movie_manager')->findMoviesByActor($actor);
        return new Response($this->get('jms_serializer')->serialize($movies, 'json',
            SerializationContext::create()->setGroups(['actor'])->enableMaxDepthChecks()),
            Response::HTTP_OK, ['Content-type' => 'application/json']
        );
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Get average user rating by unique movie id",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when movie is not found"
     *     }
     * )
     *
     * @Get("/movie/{id}/rating", requirements={"id" = "\d+"})
     * @ParamConverter("movie", class="MovieBundle:Movie")
     *
     * @param Movie $movie
     * @return Response
     */
    public function getMovieRatingAction(Movie $movie)
    {
        $rating = $this->get('movie.rating_manager')->findAverageRatingByMovie($movie);
        return new JsonResponse(['rating' => $rating], Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Create rating for specific movie with current user",
     *     statusCodes={
     *         201="Returned when successfully created",
     *         404="Returned when movie is not found",
     *         409="Returned when user has already submitted a rating for this movie",
     *         422="Returned when rating has validation errors"
     *     }
     * )
     *
     * @Post("/movie/{id}/rating", requirements={"id" = "\d+"})
     * @ParamConverter("movie", class="MovieBundle:Movie")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Movie $movie
     * @param Request $request
     * @throws NonUniqueResultException
     * @return Response
     */
    public function postMovieRatingAction(Movie $movie, Request $request)
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
            ], Response::HTTP_CONFLICT);
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
