<?php

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FullDateReservationValidator extends ConstraintValidator
{
    /**
   * @var EntityManager
   */
  private $em;

  /**
   * @param EntityManager $em
   */
  public function __construct(EntityManagerInterface $em)
  {
      $this->em = $em;
  }

  /**
   * @param mixed $value
   * @param Constraint $constraint
   */
  public function validate($value, Constraint $constraint)
  {
      $numberBillets = $this->em->getRepository('AppBundle:Reservation')->getNumberBilletsForDate($value->getDateReservation()->format('Y-m-d'));

      // On vérifie si la date de résesrvation ne va pas dépasser les 1000 billets avec ceux de la réservation ou faut déjà 1000 billets
      if($numberBillets > (10 - $value->getBillets()->count()) || $numberBillets === 10)
      {
          $this->context->buildViolation($constraint->message)
              ->setParameter('date', $value->getDateReservation()->format('d/m/Y'))
              ->atPath('dateReservation')
              ->addViolation();
      }

  }
}
