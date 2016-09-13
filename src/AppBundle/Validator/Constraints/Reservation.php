<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

/**
 * @Annotation
 */
class Reservation extends Constraint
{
    public $message = 'Vous ne pouvez commander autant de billets pour le date';
    public $messageBillet = 'Il doit y avoir au moins un billet';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}