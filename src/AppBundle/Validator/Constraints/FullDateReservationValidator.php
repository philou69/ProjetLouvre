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
      $dateComplete = $this->em->getRepository('AppBundle:CompteReservation')->findOneBy(array('dateReservation' => $value));

      if ($dateComplete !== null && $dateComplete->getTotal() == 1000) {
          $this->context->buildViolation($constraint->messageFull)
              ->setParameter('date', $value->format('d/m/Y'))
          ->addViolation();
      }
      elseif ($value->format('d/m') == date('01/05') || $value->format('d/m') == date('01/11') || $value->format('d/m') == date('25/12'))
      {
          $this->context->buildViolation($constraint->messageClose)
              ->addViolation();
      }
      elseif ($value->format('N') == 2 || $value->format('N') == 7)
      {
          $this->context->buildViolation($constraint->messageClose)
              ->addViolation();
      }
  }
}
