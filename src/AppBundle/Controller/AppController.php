<?php

namespace AppBundle\Controller;

use AppBundle\Mailer\MailGunMailer;
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
        return $this->render(':App:index.html.twig');
    }

    public function tarifsAction()
    {
        return $this->render(':App:tarifs.html.twig');
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

        return $this->render(':Reservation:reservation.html.twig', array('form' => $form->createView(), 'listDatesCompletes' => $listDatesCompletes));
    }


    /**
     * @Security("request.getClientIp() ==  reservation.getIp() && reservation.isPayer() == false", message="Vous ne pouvez acceder à cette page")
     *
     * @param Reservation $reservation
     * @param Request $request
     * @param StripePayment $payment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmationAction(Reservation $reservation, Request $request, StripePayment $payment, MailGunMailer $mailGunMailer)
    {
        return $this->render(':Payment:payment.html.twig', array('reservation' => $reservation));
    }

    /**
     * @Security("request.getClientIp() ==  reservation.getIp() && reservation.isPayer() == true", statusCode=404, message="Vous ne pouvez acceder à cette page")
     * @Security("request.headers.get('referer') !=  null",statusCode=404, message="Vous ne pouvez plus acceder à cette page")
     * @param Request $request
     * @param Reservation $reservation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function doneAction(Request $request, Reservation $reservation)
    {
        return $this->render(':Payment:summary.html.twig', ['reservation' => $reservation]);
    }

    public function downloadAction(Reservation $reservation)
    {
        $pdfUrl = $this->getParameter("pdf_path") . $reservation->getCodeReservation() . ".pdf";

        return $this->file($pdfUrl);
    }
}
