<?php

namespace IHelpShopping\DataFixtures\Traits;

use Doctrine\Common\Persistence\ObjectManager;

trait DoctrineManagerTrait
{
    protected $manager;

    protected function setManager(ObjectManager $manager): void
    {
        $this->manager = $manager;
    }
}
