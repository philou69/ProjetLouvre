<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Payum\Core\Request\GetHumanStatus;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\CompteReservation;
use Payum\Core\Model\Payment;
use AppBundle\Event\ReservationEvent;

class PaymentController extends Controller
{
    public function prepareAction(Reservation $reservation, $gateway)
    {
        $gatewayName = $gateway;

        $storage = $this->get('payum')->getStorage('AppBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount($reservation->getPrix().'00');
        $payment->setDescription('Billet du louvre');
        $payment->setClientId($reservation->getId());
        $payment->setClientEmail($reservation->getEmail());

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName, $payment, 'app_done'
    );

        return $this->redirect($captureToken->getTargetUrl());
    }

    public function doneAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));

        $payment = $status->getFirstModel();
        $reservation = $em->getRepository('AppBundle\Entity\Reservation')->findOneBy(array('id' => $payment->getClientId()));

        if ($status->isCaptured()) {
            $this->get('event_dispatcher')->dispatch('reservation.captured', new ReservationEvent($reservation));
            $reservation->addPayement();

            $dateReservation = $em->getRepository('AppBundle:CompteReservation')->findOneBy(array('dateReservation' => $reservation->getDateReservation()));
            if ($dateReservation === null) {
                $dateReservation = new CompteReservation();
                $dateReservation->setDateReservation($reservation->getDateReservation());
                $dateReservation->setTotal($reservation->getBillets()->count());
            } else {
                $dateReservation->setTotal($reservation->getBillets()->count());
            }
            $em->persist($dateReservation);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Billet du louvre')
                ->setFrom('phil.pichet@gmail.com')
                ->setTo($reservation->getEmail())
                ->setBody(
                    $this->renderView('AppBundle:App:email.txt.twig', array('reservation' => $reservation), 'text/plain'))
                ->addPart($this->renderView('AppBundle:App:email.html.twig', array('reservation' => $reservation),  'text/html'))
                ->attach(\Swift_Attachment::frompath($this->getParameter('kernel.root_dir').'/../web/uploads/pdf/Reservation'.$reservation->getId().'.pdf'));
            $this->get('mailer')->send($message);

            return $this->render('AppBundle:App:payer.html.twig', array('reservation' => $reservation, 'payment' => $payment));
        } else {
            return $this->redirectToRoute('app_confirmation', array('id' => $reservation->getId()));
        }
    }

    public function facebookAction(Reservation $reservation)
    {
        return $this->render('AppBundle:App:facebook.html.twig', array('reservation' => $reservation));
    }

    public function twitterAction()
    {
    }
}
