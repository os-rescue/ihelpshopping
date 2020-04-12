<?php

namespace IHelpShopping\Validator\Constraints;

use API\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CurrentPasswordValidator extends ConstraintValidator
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param UserInterface $object
     * @param Constraint $constraint
     */
    public function validate($object, Constraint $constraint): void
    {
        if (null === $object->getPassword()) {
            return;
        }

        if (null === $plainPassword = $object->getPlainPassword()) {
            return;
        }

        if ($this->encoder->isPasswordValid($object, $plainPassword)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('plainPassword')
                ->addViolation();
        }
    }
}
