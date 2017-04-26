<?php

namespace MovieBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateTestUsers extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        return 1;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $girls = ['Anna', 'Beth', 'Cathy'];
        foreach ($girls as $girl) {
            $user = $this->createUser($girl);
            $manager->persist($user);
        }
        $boys = ['Adam', 'Ben', 'Cam'];
        foreach ($boys as $boy) {
            $user = $this->createUser($boy);
            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @param string $name
     * @return \FOS\UserBundle\Model\UserInterface
     */
    private function createUser($name)
    {
        $user = $this->container->get('fos_user.util.user_manipulator')->create($name, $name, sprintf("%s@gmail.com", $name), true, false);
        $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        return $user;
    }
}
