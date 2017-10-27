<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;


/**
 * Class CloseDate
 * @package AppBundle\Validator\Constraints
 *
 * @Annotation
 */
class CloseDate extends Constraint
{

    public $message = 'Le muséum est fermé le date';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

}