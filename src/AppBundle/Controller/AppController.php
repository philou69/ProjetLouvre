<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompteReservation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Billet;
use AppBundle\Form\ReservationType;
use AppBundle\Event\ReservationEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        // Création d'une instance réservation contenant une instance de billet
        $reservation = new Reservation();
        $reservation->addBillet(new Billet());

        // On récuperer la liste des dates où le musée a atteint le maximum de place
        $listDatesCompletes = $em->getRepository('AppBundle:Reservation')->getDateFull();

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on appelle le service pour calculer le prix
            $calculateur = $this->get('calcul_prix.calcul_prix');
            $calculateur->calculPrix($reservation);

            // On attribut un code unique à la réservation, on persiste et on redirige vers la page confirmation
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('app_confirmation', array('id' => $reservation->getId()));
        }

        return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDatesCompletes' => $listDatesCompletes));
    }

    public function confirmationAction(Reservation $reservation, Request $request)
    {
        // Si la reservation est déjà payer, on léve une erreur 404 au lieu de rediriger sur la vue des billets
        // Cela empechera quiconque de récuperer des billets ne lui appartenant pas
        if ($reservation->isPayer()) {
            throw $this->createNotFoundException('Réservation inexistante');
        }

        if ($request->isMethod('POST')){
            $payment = $this->get('payment.stripe');

            $status = $payment->payed(
                $request->request->get("stripeEmail"),
                $request->request->get('stripeToken'),
                $reservation
            );

            if( $status instanceof \Exception){
                $this->addFlash('warning', $status->getMessage());
            } elseif ( $reservation->isPayer() ){
                var_dump($status);
                //                return $this->redirectToRoute('app_done', ['id' => $reservation->getId()]);
            }
        }

        return $this->render('AppBundle:App:confirmation.html.twig', array('reservation' => $reservation));
    }

    public function doneAction(Request $request, Reservation $reservation)
    {
        // Si la reservation n'est pas payer, on redirige sur la page confirmation
        if (!$reservation->isPayer()) {
            return $this->redirectToRoute('app_confirmation', ['id' => $reservation->getId()]);
        }
        // Sinon on affiche la vue
        return $this->render('AppBundle:App:payer.html.twig', array('reservation' => $reservation));
    }
}
