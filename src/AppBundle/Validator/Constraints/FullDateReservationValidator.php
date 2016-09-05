<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class FullDateReservationValidator extends ConstraintValidator
{

  /**
   * @var EntityManager
   */
  private $em;

  /**
   * @param EntityManager $em
   */
  public function setEntityManager(EntityManager $em)
  {
    $this->em = $em;
  }

  /**
   * @param mixed $value
   * @param Constraint $constraint
   */
  public function validate($value, Constraint $constraint)
  {
    $dateComplete = $this->em->getRepository('AppBundle:CompteReservation')->findBy(array('dateReservation' => $value));

    if ($dateComplete !== null && $dateComplete->getTotal() == 1000)
    {
      $this->context->buildViolation($constraint->messageDatePleine)
          ->addViolation();

    }
  }
}
