<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CloseDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // On vérifie la date de la réservation

        // Si la date est le 1 Mai, le 11 Novembre ou le 25 Décembre, on affiche un message d'erreur
        if ($value->format('d/m') == date('01/05') || $value->format('d/m') == date('01/11') || $value->format('d/m') == date('25/12')) {

            $this->context->buildViolation($constraint->message)
                ->setParameter('date', $value->format('d/m'))
              ->addViolation();

        } elseif ($value->format('N') == 2 || $value->format('N') == 7) {

            // Si le jour de la date est un mardi ou dimanche, on affiche un message d'erreur
            $this->context->buildViolation($constraint->message)
                ->setParameter('date', $value->format('l') === 'Sunday' ? 'Dimanche' : 'Mardi')
              ->addViolation();

        }
    }
}