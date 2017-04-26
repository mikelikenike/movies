<?php

namespace MovieBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MovieBundle\Entity\Rating;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateRatings extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $userIds = [1, 4];
        $userManager = $this->container->get('fos_user.user_manager');
        foreach ($userIds as $userId) {
            $user = $userManager->findUserBy(['id' => $userId]);
            $movies = $this->container->get('doctrine.orm.default_entity_manager')->getRepository('MovieBundle:Movie')->findAll();
            foreach ($movies as $movie) {
                $rating = new Rating();
                $rating->setMovie($movie);
                $rating->setUser($user);
                $rating->setRating(rand(0, 10));
                $manager->persist($rating);
            }
        }
        $manager->flush();
    }
}
