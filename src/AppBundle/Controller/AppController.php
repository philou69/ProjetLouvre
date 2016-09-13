<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Billet;
use AppBundle\Form\ReservationType;
use AppBundle\Event\ReservationEvent;

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

        $listDatesCompletes = $em->getRepository('AppBundle:CompteReservation')->findBy(array('total' => 1000));

        $form = $this->createForm(ReservationType::class, $reservation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $calculateur = $this->get('calcul_prix.calcul_prix');
            $calculateur->calculPrix($reservation);

            $reservation->setCodeReservation(uniqid());
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('app_confirmation', array('id' => $reservation->getId()));
        }

        return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDatesCompletes' => $listDatesCompletes));
    }

    public function confirmationAction(Reservation $reservation)
    {
        if ($reservation === null)
        {
            throw new NotFoundHttpException('La réservation '.$reservation->getId().' n\'a pas été trouver');
        } elseif ($reservation->isPayer()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('AppBundle:App:confirmation.html.twig', array('reservation' => $reservation));
    }
}
