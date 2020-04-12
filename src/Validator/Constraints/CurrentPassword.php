<?php

namespace IHelpShopping\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrentPassword extends Constraint
{
    public $message = 'already_used';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
