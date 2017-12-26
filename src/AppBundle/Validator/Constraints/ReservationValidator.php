<?php

namespace AppBundle\Validator\Constraints;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ReservationValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
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