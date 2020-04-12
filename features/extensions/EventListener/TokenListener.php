<?php

namespace IHelpShopping\BehatExtension\EventListener;

use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TokenListener implements EventSubscriberInterface
{
    private $container;

    public function __construct(Kernel $kernel)
    {
        $this->container = $kernel->getContainer();
    }

    public static function getSubscribedEvents()
    {
        return [
            FeatureTested::BEFORE => 'beforeFeature',
        ];
    }

    public function beforeFeature(): void
    {
        $token = $this->container->get('security.token_storage')->getToken();
        if (!$token instanceof TokenInterface) {
            return;
        }

        $this->container->get('security.token_storage')->setToken(null);
    }
}
