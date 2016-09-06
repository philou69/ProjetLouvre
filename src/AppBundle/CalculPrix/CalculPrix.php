<?php

namespace AppBundle\CalculPrix;

use AppBundle\Entity\Reservation;

class CalculPrix
{

    public function calculPrix(Reservation $reservation)
    {
        $reservation->calculPrix();
    }
}