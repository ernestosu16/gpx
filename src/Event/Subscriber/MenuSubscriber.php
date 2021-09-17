<?php

namespace App\Event\Subscriber;

use App\Entity\Menu;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class MenuSubscriber extends _Subscriber_
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Menu)
            return;

        $prefix = null;
        if ($parent = $object->getParent())
            $prefix = $parent->getCodigo();
        $object->setCodigo($prefix . '_' . $object->getCodigo());
    }
}
