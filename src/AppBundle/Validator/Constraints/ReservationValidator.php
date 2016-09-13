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
        $dateComplete = $this->em->getRepository('AppBundle:CompteReservation')->findOneBy(array('dateReservation' =>$value->getDateReservation()));

        if($dateComplete !== null && $dateComplete->getTotal() > (1000-$value->getBillets()->count()))
        {
            $this->context->buildViolation($constraint->message)
                ->setParameter('date', $value->getDateReservation()->format('d/m/Y'))
                ->addViolation();
        }elseif ($value->getBillets()->count() == 0)
        {
            $this->context->buildViolation($constraint->messageBillet)
                ->addViolation();
        }
    }
}