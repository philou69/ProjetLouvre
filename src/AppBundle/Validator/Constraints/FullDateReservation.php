<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FullDateReservation extends Constraint
{
    public $messageFull = "Le date est plein";
    public $messageClose = "Le date, le musée est fermé";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
