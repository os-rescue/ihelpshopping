<?php

namespace IHelpShopping\EventListener\User;

use API\UserBundle\Event\UserEvent;
use API\UserBundle\Model\UserInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserSubscriber implements EventSubscriberInterface
{
    private const ROUTE_API_USERS_PREFIX = 'api_users_';

    private $tokenStorage;
    private $dispatcher;

    public function __construct(TokenStorageInterface $tokenStorage, EventDispatcherInterface $dispatcher)
    {
        $this->tokenStorage = $tokenStorage;
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['resolveMe', EventPriorities::PRE_READ],
            ],
            KernelEvents::VIEW => ['onRegistrationSuccess', EventPriorities::POST_WRITE],
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

    public function onRegistrationSuccess(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof UserInterface || Request::METHOD_POST !== $method) {
            return;
        }

        $event = new UserEvent($user);
        $this->dispatcher->dispatch(UserEvent::EMAIL_CREATED, $event);
    }
}
