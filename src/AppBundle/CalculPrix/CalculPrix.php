<?php

namespace AppBundle\CalculPrix;

use AppBundle\Entity\Reservation;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CalculPrix implements FixtureInterface
{

    public function calculPrix(Reservation $reservation)
    {
        $reservation->calculPrix();
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        // TODO: Implement load() method.
    }
}