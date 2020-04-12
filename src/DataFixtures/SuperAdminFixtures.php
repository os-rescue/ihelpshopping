<?php

namespace IHelpShopping\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use IHelpShopping\DataFixtures\Traits\UserPersistenceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class SuperAdminFixtures extends Fixture implements ContainerAwareInterface
{
    use UserPersistenceTrait;

    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        $fixtures = Yaml::parse(file_get_contents(__DIR__ . '/fixtures.yaml'));
        if (empty($fixtures['super_admin_users'])) {
            return;
        }

        $this->setManager($manager);
        $this->initUserDependencies();

        $this->setEncoder($this->container->get('security.password_encoder'));

        foreach ($fixtures['super_admin_users'] as $data) {
            $this->createOrUpdateUser($data);
        }

        $manager->flush();
    }
}
