<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\Reservation;

class ReservationEvent extends Event
{
    protected $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function getReservation()
    {
        return $this->reservation;
    }
}
