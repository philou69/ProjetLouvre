<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FullDateReservation extends Constraint
{
  public $message = "La date  n'est pas valide";

  public function validatedBy()
  {
    return get_class($this).'Validator';
  }
}
