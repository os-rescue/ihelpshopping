<?php

namespace IHelpShopping\EventListener\User;

use API\UserBundle\Model\UserInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserSubscriber implements EventSubscriberInterface
{
    private const ROUTE_API_USERS_PREFIX = 'api_users_';

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['resolveMe', EventPriorities::PRE_READ],
            ]
        ];
    }

    public function resolveMe(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (strstr(self::ROUTE_API_USERS_PREFIX, $request->attributes->get('_route'))) {
            return;
        }

        if ('me' !== $request->attributes->get('id')) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $request->attributes->set('id', $user->getId());
    }
}
