<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FullDateReservation extends Constraint
{
    public $messageFull = "Le date est plein";
    public $messageClose = "Le musée est fermé à la date séléctionnée";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
