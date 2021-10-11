<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\Localizacion;
use App\Event\Subscriber\_DoctrineSubscriber_;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class LocalizacionDoctrineSubscriber extends _DoctrineSubscriber_
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

        if (!$object instanceof Localizacion)
            return;

//        $code = [];
//
//        if ($tipo = $object->getTipo())
//            $code[] = $tipo->getCodigo();
//
//        $code[] = $object->getCodigo();
//
//        $object->setCodigo(implode('_', $code));
    }
}
