<?php

namespace IHelpShopping\Tests\DataProvider;

use Doctrine\Persistence\ManagerRegistry;
use IHelpShopping\Entity\User;

final class UserDataProvider
{
    private $repository;

    public function __construct(ManagerRegistry $registry)
    {
        $this->repository = $registry->getManager()->getRepository(User::class);
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
