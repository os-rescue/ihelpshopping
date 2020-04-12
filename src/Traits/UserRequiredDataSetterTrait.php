<?php

namespace IHelpShopping\Traits;

use Doctrine\Common\Persistence\ObjectManager;
use IHelpShopping\Dto\UserRequiredData;
use IHelpShopping\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

trait UserRequiredDataSetterTrait
{
    public function setRequiredProperties(User $user, UserRequiredData $requiredData): User
    {
        $user->setEmail($requiredData->getEmail());
        $user->setEmailCanonical($requiredData->getEmail());
        $user->setUsername($requiredData->getEmail());
        $user->setFirstName($requiredData->getFirstName());
        $user->setLastname($requiredData->getLastName());
        $user->setAddress($requiredData->getAddress());

        if (null !== $requiredData->getRoles()) {
            $user->setRoles($requiredData->getRoles());
        }

        if (null !== $requiredData->getPlainPassword()) {
            $encodedPassword = $this->getUserPasswordEncoder()
                ->encodePassword($user, $requiredData->getPlainPassword());
            $user->setPassword($encodedPassword);
        }

        return $user;
    }

    abstract public function getManager(): ObjectManager;

    abstract public function getUserPasswordEncoder(): UserPasswordEncoderInterface;
}
