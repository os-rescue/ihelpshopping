<?php

namespace IHelpShopping\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use IHelpShopping\DataFixtures\Traits\UserPersistenceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class UserFixtures extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
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
        if (empty($fixtures['users'])) {
            return;
        }

        $this->setManager($manager);
        $this->initUserDependencies();

        $this->setEncoder($this->container->get('security.password_encoder'));

        $env = $this->container->get('kernel')->getEnvironment();

        $i = 0;
        foreach ($fixtures['users'] as $data) {
            $user = $this->createOrUpdateUser($data);

            if ('test' === $env) {
                $datetime = new \DateTime("+$i hour");
                $user->setCreatedAt($datetime)->setUpdatedAt($datetime);
                $i++;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SuperAdminFixtures::class,
        ];
    }
}
