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
            dump($reservation);
            exit;
            foreach ($listDatesCompletes as $dateComplete) {
                if ($dateComplete == $reservation->getDateReservation()) {
                    $this->request->getSession()->getFlashBag()->add('warning', 'La capacite de visisteurs du musée est atteinte le jour sélectionner.');

                    return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDate' => $listDateCompletes));
                } elseif ($reservation->getDateReservation()->format('N') == 7 || $reservation->getDateReservation()->format('N') == 2 || $reservation->getDateReservation()->format('d/m') == date('01/05') || $reservation->getDateReservation()->format('d/m') == date('01/11') || $reservation->getDateReservation()->format('d/m') == date('25/12')) {
                    $request->getSession()->getFlashBag()->add('warning', 'Le musée est fermé le jour sélectionner');

                    return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDate' => $listDatesCompletes));
                }
            }
            $this->get('event_dispatcher')->dispatch('reservation.persist', new ReservationEvent($reservation));

            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('app_confirmation', array('id' => $reservation->getId()));
        }

        return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDate' => $listDatesCompletes));
    }

    public function confirmationAction(Reservation $reservation)
    {
        if ($reservation === null) {
            return $this->redirectToRoute('app_reservation');
        } elseif ($reservation->isPayer()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('AppBundle:App:confirmation.html.twig', array('reservation' => $reservation));
    }
}
