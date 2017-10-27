<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FullDateReservation extends Constraint
{
    public $message = "Le muséum est complet le date";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
