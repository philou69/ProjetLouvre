<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BilleterieController extends Controller
{
    public function reservationAction(Reservation $reservation)
    {
        return $this->render('AppBundle:App:billet.html.twig', array('reservation' => $reservation));
    }
}