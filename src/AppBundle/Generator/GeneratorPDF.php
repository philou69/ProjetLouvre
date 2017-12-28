<?php


namespace AppBundle\Generator;


use AppBundle\Entity\Reservation;
use Knp\Snappy\Pdf;

class GeneratorPDF
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var Pdf
     */
    private $pdf;
    private $pdfPath;

    public function __construct(Pdf $pdf, \Twig_Environment $twig, $pdfPath)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
        $this->pdfPath = $pdfPath;
    }

    public function generatePDF(Reservation $reservation)
    {
        $pdfUrl = $this->pdfPath . $reservation->getCodeReservation() . '.pdf';
        $this->pdf->generateFromHtml(
            $this->twig->render(
                '@App/App/billet.html.twig',
                [
                    'reservation' => $reservation
            ]),
            $this->pdfPath . $reservation->getCodeReservation() . '.pdf'
        );

        return $pdfUrl;
    }
}