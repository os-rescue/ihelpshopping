<?php

namespace IHelpShopping\EventListener\RequesterShoppingItem;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use IHelpShopping\Entity\RequesterShoppingItem;
use IHelpShopping\Entity\User;

final class NbPendingItemsSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$entity instanceof RequesterShoppingItem) {
                continue;
            }

            $user = $entity->getCreatedBy();
            $user->setNbPendingItems($user->getNbPendingItems() + 1);

            $md = $em->getClassMetadata(User::class);
            $uow->computeChangeSets($md, $user);
        }
    }
}
