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

      // On vérifie la date de la réservation
      // Si la date a déjà 1000 places, on affiche un message d'erreur
      if ($dateComplete !== null && $dateComplete->getTotal() == 1000) {
          $this->context->buildViolation($constraint->messageFull)
              ->setParameter('date', $value->format('d/m/Y'))
          ->addViolation();
      }
      // Si la date est le 1 Mai, le 11 Novembre ou le 25 Décembre, on affiche un message d'erreur
      elseif ($value->format('d/m') == date('01/05') || $value->format('d/m') == date('01/11') || $value->format('d/m') == date('25/12'))
      {
          $this->context->buildViolation($constraint->messageClose)
              ->addViolation();
      }
      // Si le jour de la date est un mardi ou dimanche, on affiche un message d'erreur
      elseif ($value->format('N') == 2 || $value->format('N') == 7)
      {
          $this->context->buildViolation($constraint->messageClose)
              ->addViolation();
      }
  }
}
