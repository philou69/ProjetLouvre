<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Reservation;

class BilletTest extends \PHPUnit_Framework_TestCase
{
	public function testBilletNormal()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet = new Billet();
		$reservation->addBillet($billet);
		$billet->setDateNaissance(new \DateTime('1987-08-25'));
		$this->assertEquals('16', $billet->getPrix());
	}

	public function testBilletEnfantGratuit()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet = new Billet();
		$reservation->addBillet($billet);
		$billet->setDateNaissance(new \DateTime('2015-08-25'));
		$this->assertEquals('0', $billet->getPrix());
	}

	public function testBilletEnfant()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet = new Billet();
		$reservation->addBillet($billet);

		$billet->setDateNaissance(new \DateTime('2009-08-25'));
		$this->assertEquals('8', $billet->getPrix());
	}

	public function testBilletSenior()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet = new Billet();
		$reservation->addBillet($billet);

		$billet->setDateNaissance(new \DateTime('1950-08-25'));

		$this->assertEquals('12', $billet->getPrix());
	}

	public function testBilletReduit()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet = new Billet();
		$reservation->addBillet($billet);
		$billet->addReduction()
			   ->setDateNaissance(new \DateTime('1987-08-25'));

		$this->assertEquals('10', $billet->getPrix());
	}

	public function testAge()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet = new Billet();
		$reservation->addBillet($billet);
		$billet->setDateNaissance(new \DateTime('1987-08-25'));

		$this->assertEquals('28', $billet->getAge());
	}

	public function testIsReduct()
	{
		$billet = new Billet();

		$billet->addReduction();

		$this->assertTrue($billet->isReduit());
	}

	public function testIsNotReduct()
	{
		$billet = new Billet();

		$this->assertFalse($billet->isReduit());
	}

}
