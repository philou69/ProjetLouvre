<?php

namespace AppBundle\Controller;

use AppBundle\Payment\StripePayment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

        // Création d'une instance réservation contenant une instance de billet
        $reservation = new Reservation();
        $reservation->addBillet(new Billet())
            ->setIp($request->getClientIp());

        // On récuperer la liste des dates où le musée a atteint le maximum de place
        $listDatesCompletes = $em->getRepository('AppBundle:Reservation')->getDateFull();

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On attribut un code unique à la réservation, on persiste et on redirige vers la page confirmation
            $em->persist($reservation);
            $em->flush();
            return $this->redirectToRoute('app_confirmation', array('id' => $reservation->getId()));
        }

        return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDatesCompletes' => $listDatesCompletes));
    }


    /**
     * @Security("request.getClientIp() ==  reservation.getIp() && reservation.isPayer() == false")
     *
     * @param Reservation $reservation
     * @param Request $request
     * @param StripePayment $payment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmationAction(Reservation $reservation, Request $request, StripePayment $payment)
    {
        if ($request->isMethod('POST')) {
//            $payment = $this->get('payment.stripe');

            $status = $payment->payed(
                $request->request->get("stripeEmail"),
                $request->request->get('stripeToken'),
                $reservation
            );

            if ($status instanceof \Exception) {
                $this->addFlash('warning', $status->getMessage());
            } elseif ($reservation->isPayer()) {
                return $this->redirectToRoute('app_done', ['id' => $reservation->getId()]);
            }
        }

        return $this->render('AppBundle:App:confirmation.html.twig', array('reservation' => $reservation));
    }

    /**
     * @Security("request.getClientIp() ==  reservation.getIp() && reservation.isPayer() == true", statusCode=404, message="Vous ne pouvez acceder à cette page")
     * @param Request $request
     * @param Reservation $reservation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function doneAction(Request $request, Reservation $reservation)
    {
            return $this->render('@App/App/payer.html.twig', ['reservation' => $reservation]);

    }
}
