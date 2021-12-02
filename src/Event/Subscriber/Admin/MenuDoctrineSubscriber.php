<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\Nomenclador\Menu;
use App\Event\Subscriber\_DoctrineSubscriber_;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class MenuDoctrineSubscriber extends _DoctrineSubscriber_
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

        $code = [];
        if ($root = $object->getRoot())
            $code[] = $root->getCodigo();

        if ($object->getCodigo())
            $code[] = $object->getCodigo();
        $object->setCodigo(implode('_', $code));
    }
}
