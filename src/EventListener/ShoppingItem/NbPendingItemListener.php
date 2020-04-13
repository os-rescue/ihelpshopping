<?php

namespace Eqsgroup\EventListener\Policy;

use IHelpShopping\Entity\ShoppingItem;

final class NbPendingItemListener
{
    /** @ORM\PrePersist */
    public function prePersist(ShoppingItem $shoppingItem): void
    {
        $user = $shoppingItem->getCreatedBy();
        $user->setNbPendingItems($user->getNbPendingItems() + 1);
    }

    /** @ORM\PreRemove */
    public function preRemove(ShoppingItem $shoppingItem): void
    {
        $user = $shoppingItem->getCreatedBy();
        $user->setNbPendingItems($user->getNbPendingItems() - 1);
    }
}
