<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Billet;
use AppBundle\Form\ReservationType;


class AppController extends Controller
{
	public function indexAction()
	{
		return $this->render('AppBundle:App:index.html.twig');
	}
	public function tarifsAction()
	{
		return $this->render('AppBundle:App:tarifs.html.twig');
	}

	public function reservationAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$reservation = new Reservation();
		$reservation->addBillet(new Billet());

		$listDate = $em->getRepository('AppBundle:CompteReservation')->findBy(array('total' => 1000));

		$form = $this->createForm(ReservationType::class, $reservation);

		if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			if($reservation->memeNom() === true)
	        {
	            if($reservation->isDemiJournee() === true)
	            {
	                $reservation->setPrix(17.5);
	            }
	            else{
	                $reservation->setPrix(35);
	            }
	        }
	        else
	        {
	        	$prix = 0;
	            foreach ($reservation->getBillets() as $billet)
	            {
	                if($reservation->isDemiJournee() === true)
	                {
	                    $prix = $prix + ($billet->getPrix()/2);
	                }
	                else
	                {
	                    $prix = $prix + $billet->getPrix();
	                }
	            }
	            $reservation->setPrix($prix);
	        }
			dump($reservation);
			exit;

			$em->persist($reservation);
			$em->flush();

			return $this->redirectToRoute('app_confirmation', array('id'=> $reservation->getId()));
		}

		return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDate' =>$listDate));
	}

	public function confirmationAction(Reservation $reservation)
	{
		if($reservation === null)
		{
			return $this->redirectToRoute('app_reservation');
		}
		elseif ($reservation->isPayer())
		{
			return $this->redirectToRoute('app_home');
		}

		return $this->render('AppBundle:App:confirmation.html.twig', array('reservation' => $reservation));
	}

}
