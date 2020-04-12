<?php

namespace IHelpShopping\DataFixtures\Traits;

use API\UserBundle\Model\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IHelpShopping\Dto\UserRequiredData;
use IHelpShopping\Entity\User;
use IHelpShopping\Traits\UserRequiredDataSetterTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

trait UserPersistenceTrait
{
    use DoctrineManagerTrait;
    use UserRequiredDataSetterTrait;

    private $userRepository;
    private $establishmentRepository;
    private $encoder;

    public function createOrUpdateUser(array $data): UserInterface
    {
        if (null === $user = $this->findUserBy($data)) {
            $user = new User();

            $user->setConfirmationToken($data['confirmation_token'] ?? null);
        }

        $requiredData = new UserRequiredData(
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['address'] ?? null,
            !empty($data['role']) ? [$data['role']] : null,
            $data['plain_password'] ?? null
        );
        $user = $this->setRequiredProperties($user, $requiredData);
        $user->setEnabled($data['enabled'] ?? false);
        $user->setLocked($data['locked'] ?? false);
        $user->setMiddleName($data['middle_name'] ?? null);
        $user->setMobileNumber($data['mobile_number'] ?? null);
        $user->setPhoneNumber($data['phone_number'] ?? null);

        if (!empty($data['retry_ttl'])) {
            $retryTtl = $data['retry_ttl'];
            $user->setPasswordRequestedAt(new \DateTime("+$retryTtl seconds"));
        }

        $this->manager->persist($user);

        return $user;
    }

    private function findUserBy(array $data): ?User
    {
        $criteria = !empty($data['confirmation_token']) ?
            ['confirmationToken' => $data['confirmation_token']] :
            ['email' => $data['email']];

        return $this->userRepository->findOneBy($criteria);
    }

    protected function setEncoder(UserPasswordEncoderInterface $encoder): void
    {
        $this->encoder = $encoder;
    }

    protected function initUserDependencies(): void
    {
        $this->userRepository = $this->manager->getRepository(User::class);
    }

    public function getManager(): ObjectManager
    {
        return $this->manager;
    }

    public function getUserPasswordEncoder(): UserPasswordEncoderInterface
    {
        return $this->encoder;
    }
}
