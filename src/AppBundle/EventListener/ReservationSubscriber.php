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

    public function setSnappy(LoggableGenerator $snappy)
    {
        $this->snappy = $snappy;
    }

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function setPdfPath($pdfPath)
    {
        $this->pdfPath = $pdfPath;
    }

    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return array(
      'reservation.persist' => array(array('memeJour'), array('calculPrix')),
      'reservation.captured' => array(array('generatePDF'), array('sendMail')),
    );
    }

    public function memeJour($event)
    {
        $reservation = $event->getReservation();

        $nowDate = date('Y-m-d');
        $nowHeure = date('H');

        if ($reservation->getDateReservation()->format('Y-m-d') == $nowDate && $nowHeure > 14) {
            $reservation->addDemiJournee(true);
        }
    }

    public function calculPrix($event)
    {
        $reservation = $event->getReservation();
        if ($reservation->memeNom() === true) {
            if ($reservation->isDemiJournee() === true) {
                $reservation->setPrix(17.5);
            } else {
                $reservation->setPrix(35);
            }
        } else {
            $prix = 0;
            foreach ($reservation->getBillets() as $billet) {
                if ($reservation->isDemiJournee() === true) {
                    $prix = $prix + ($billet->getPrix() / 2);
                } else {
                    $prix = $prix + $billet->getPrix();
                }
            }
            $reservation->setPrix($prix);
        }
    }

    public function generatePDF($event)
    {
        $reservation = $event->getReservation();
        $billetsPdf = $this->snappy->generateFromHtml($this->twig->render('AppBundle:App:billet.html.twig', array('reservation' => $reservation)), $this->pdfPath.'/Reservation'.$reservation->getId().'.pdf');
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
      ->addPart($this->twig->render('AppBundle:App:email.html.twig', array('reservation' => $reservation),  'text/html'))
      ->attach(\Swift_Attachment::frompath($this->pdfPath.'/Reservation'.$reservation->getId().'.pdf'));

        $this->mailer->send($message);
    }
}
