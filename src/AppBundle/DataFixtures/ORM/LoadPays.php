<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Pays;
class LoadPays implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $countries = array(
            'France',
            'Espagne',
            'Angleterre',
            'Allemagne',
            'Suisse',
            'Autre'
        );

        foreach ($countries as $country) {
            $pays = new Pays();
            $pays->setName($country);

            $manager->persist($pays);
        }

        $manager->flush();
    }
}