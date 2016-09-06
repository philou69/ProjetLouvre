<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Reservation;
use AppBundle\Entity\Billet;

class ReservationTest extends \PHPUnit_Framework_TestCase
{
	public function testMemeNom()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet1 = new Billet();
		$billet2 = new Billet();
		$billet3 = new Billet();
		$billet4 = new Billet();

		$reservation->addBillet($billet1);
		$reservation->addBillet($billet2);
		$reservation->addBillet($billet3);
		$reservation->addBillet($billet4);

		$billet1->setNom('Pichet')
				->setDateNaissance(new \DateTime('1987-08-25'));


		$billet2->setNom('Pichet')
				->setDateNaissance(new \DateTime('1992-10-28'));


		$billet3->setNom('Pichet')
				->setDateNaissance(new \DateTime('2010-05-08'));


		$billet4->setNom('Pichet')
				->setDateNaissance(new \DateTime('2006-09-20'));

		$this->assertTrue($reservation->memeNom());
	}
	public function testNotMemeNom()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet1 = new Billet();
		$billet2 = new Billet();
		$billet3 = new Billet();
		$billet4 = new Billet();

		$reservation->addBillet($billet1);
		$reservation->addBillet($billet2);
		$reservation->addBillet($billet3);
		$reservation->addBillet($billet4);

		$billet1->setNom('Pichet')
				->setDateNaissance(new \DateTime('1987-08-25'));

		$billet2->setNom('Pihet')
				->setDateNaissance(new \DateTime('1992-10-28'));


		$billet3->setNom('Pichet')
				->setDateNaissance(new \DateTime('2010-05-08'));


		$billet4->setNom('Pichet')
				->setDateNaissance(new \DateTime('2006-09-20'));

		$this->assertFalse($reservation->memeNom());
	}

	public function testIsFamille()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet1 = new Billet();
		$billet2 = new Billet();
		$billet3 = new Billet();
		$billet4 = new Billet();

		$reservation->addBillet($billet1);
		$reservation->addBillet($billet2);
		$reservation->addBillet($billet3);
		$reservation->addBillet($billet4);

		$billet1->setNom('Pichet')
				->setDateNaissance(new \DateTime('1987-08-25'));

		$billet2->setNom('Pichet')
				->setDateNaissance(new \DateTime('1992-10-28'));

		$billet3->setNom('Pichet')
				->setDateNaissance(new \DateTime('2010-05-08'));

		$billet4->setNom('Pichet')
				->setDateNaissance(new \DateTime('2006-09-20'));

		$reservation->calculPrix();
		$this->assertEquals('35', $reservation->getPrix());
	}

	public function testIsNotFamille()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');

		$billet1 = new Billet();
		$billet2 = new Billet();
		$billet3 = new Billet();
		$billet4 = new Billet();

		$reservation->addBillet($billet1);
		$reservation->addBillet($billet2);
		$reservation->addBillet($billet3);
		$reservation->addBillet($billet4);

		$billet1->setNom('Pichet')
				->setDateNaissance(new \DateTime('1987-08-25'));

		$billet2->setNom('Piceit')
				->setDateNaissance(new \DateTime('1992-10-28'));

		$billet3->setNom('Pichet')
				->setDateNaissance(new \DateTime('2000-05-08'));

		$billet4->setNom('Pichet')
				->setDateNaissance(new \DateTime('2002-09-20'));

		$reservation->calculPrix();
		$this->assertEquals('64', $reservation->getPrix());
	}

	public function testisDemiJournee(){
		$reservation = new Reservation();

		$reservation->addDemiJournee();

		$this->assertTrue($reservation->isDemiJournee());
	}

	public function testisnotDemiJournee(){
		$reservation = new Reservation();

		$this->assertFalse($reservation->isDemiJournee());
	}

	public function testBilletDemiJournee()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('01-08-2016');
		$reservation->addDemiJournee();

		$billet = new Billet();
		$reservation->addBillet($billet);

		$billet->setNom('pichet')
				->setDateNaissance(new \DateTime('1987-08-25'));

		$reservation->calculPrix();
		$this->assertEquals('8', $reservation->getPrix());
	}

	public function testDate()
	{
		$reservation = new Reservation();
		$reservation->setDateReservation('21-10-2015');

		$billet = new Billet();
		$reservation->addBillet($billet);

		$billet->setDateNaissance(new \DateTime('1987-08-25'));

		$reservation->calculPrix();
		$this->assertEquals('16', $reservation->getPrix());
	}

	public function testForm()
	{
		$reservation = new Reservation();
		$billet = new Billet();

		$reservation->addBillet($billet);
		$reservation->setDateReservation('01-08-2016');

		$billet->setDateNaissance(new \DateTime('1987-08-25'));
		$reservation->calculPrix();
		$this->assertEquals('16', $reservation->getPrix());
	}
}
