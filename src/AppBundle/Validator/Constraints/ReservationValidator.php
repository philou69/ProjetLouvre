<?php

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ReservationValidator extends ConstraintValidator
{
    private $em;
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        // On s'assure que la réservation à au moins un billet
        if ($value->getBillets()->count() == 0)
        {
            $this->context->buildViolation($constraint->message)
                ->atPath('billets')
                ->addViolation();
        }
    }
}