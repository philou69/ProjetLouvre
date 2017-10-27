<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;

class ReservationSubscriber implements EventSubscriberInterface
{
    private $snappy;
    private $twig;
    private $pdfPath;
    private $mailer;

    public function __construct(LoggableGenerator $snappy, \Twig_Environment $twig, $pdfPath, \Swift_Mailer $mailer)
    {
        $this->snappy = $snappy;
        $this->twig = $twig;
        $this->pdfPath = $pdfPath;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return array(
      'reservation.captured' => array(array('generatePDF'), array('sendMail')),
    );
    }





    public function generatePDF($event)
    {
//
//        $reservation = $event->getReservation();
//        $billetsPdf = $this->snappy->generateFromHtml($this->twig->render('AppBundle:App:billet.html.twig', array('reservation' => $reservation)), $this->pdfPath.'/Reservation'.$reservation->getId().'.pdf');
    }

    public function sendMail($event)
    {
        $reservation = $event->getReservation();

        $message = \Swift_Message::newInstance()
      ->setSubject('Billet du louvre')
      ->setFrom('phil.pichet@gmail.com')
      ->setTo($reservation->getEmail())
      ->setBody(
        $this->twig->render('AppBundle:App:email.txt.twig', array('reservation' => $reservation), 'text/plain'))
      ->addPart($this->twig->render('AppBundle:App:email.html.twig', array('reservation' => $reservation),  'text/html'));
//      ->attach(\Swift_Attachment::frompath($this->pdfPath.'/Reservation'.$reservation->getId().'.pdf'));

        $this->mailer->send($message);
    }
}
