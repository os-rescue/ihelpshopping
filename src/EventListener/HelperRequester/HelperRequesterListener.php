<?php

namespace IHelpShopping\EventListener\HelperRequester;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use IHelpShopping\Entity\HelperRequester;

final class HelperRequesterListener implements EventSubscriber
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

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof HelperRequester) {
                continue;
            }

            $changeSet = $uow->getEntityChangeSet($entity);
            if ($this->isNullHelper($changeSet) || $this->isNullRequester($changeSet)) {
                $em->remove($entity);
            }
        }
    }

    private function isNullRequester(array $changeSet): bool
    {
        return !empty($changeSet['requester']) && null === $changeSet['requester'][1];
    }

    private function isNullHelper(array $changeSet): bool
    {
        return !empty($changeSet['helper']) && null === $changeSet['helper'][1];
    }
}
