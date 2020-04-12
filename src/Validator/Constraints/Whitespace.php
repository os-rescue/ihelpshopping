<?php

namespace IHelpShopping\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class Whitespace extends Constraint
{
    public $message = 'invalid_whitespaces';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
