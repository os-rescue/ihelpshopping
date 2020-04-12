<?php

namespace IHelpShopping\Tests\DataProvider;

use IHelpShopping\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;

final class UserDataProvider
{
    private $repository;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->repository = $doctrine->getManager()->getRepository(User::class);
    }

    public function findBy(array $creteria): array
    {
        $this->repository->clear();

        return $this->repository->findBy($creteria);
    }

    public function findOneBy(array $creteria): ?User
    {
        $this->repository->clear();

        return $this->repository->findOneBy($creteria);
    }
}
