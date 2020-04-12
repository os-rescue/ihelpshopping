<?php

namespace IHelpShopping\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class ValidPhone extends Constraint
{
    public $message = 'phone.invalid';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
