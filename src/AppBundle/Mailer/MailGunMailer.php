<?php


namespace AppBundle\Mailer;


use AppBundle\Entity\Reservation;
use AppBundle\Generator\GeneratorPDF;

class MailGunMailer
{
    private $twig;
    private $mailer;
    /**
     * @var GeneratorPDF
     */
    private $generatorPDF;

    /**
     * MailGunMailer constructor.
     * @param \Twig_Environment $twig_Environment
     */
    public function __construct(\Twig_Environment $twig_Environment, \Swift_Mailer $mailer, GeneratorPDF $generatorPDF)
    {
        $this->twig = $twig_Environment;
        $this->mailer = $mailer;
        $this->generatorPDF = $generatorPDF;
    }

    public function sendingMessage(Reservation $reservation)
    {
        if($reservation->isPayer()) {
            $pdfURL = $this->generatorPDF->generatePDF($reservation);
            $message = (new \Swift_Message("Vos billets"))
                ->setFrom('phil@pichet.eu')
                ->setTo($reservation->getEmail())
                ->setBody(
                    $this->twig->render(":Email:email.txt.twig", ['reservation' => $reservation], 'text/plain')
                )
                ->addPart($this->twig->render(':Email:email.html.twig', ['reservation' => $reservation]), 'text/html')
                ->attach(\Swift_Attachment::fromPath($pdfURL))
            ;

            $this->mailer->send($message);
        }
    }
}