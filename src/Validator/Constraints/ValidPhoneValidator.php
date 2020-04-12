<?php

namespace IHelpShopping\Validator\Constraints;

use IHelpShopping\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidPhoneValidator extends ConstraintValidator
{
    /**
     * @param string $phoneNumber
     * @param Constraint $constraint
     */
    public function validate($phoneNumber, Constraint $constraint): void
    {
        if (!empty($phoneNumber) && !preg_match('/^[0-9\(\)\+\-\s]*$/', $phoneNumber)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
