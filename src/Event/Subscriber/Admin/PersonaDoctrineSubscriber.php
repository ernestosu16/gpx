<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\Persona;
use App\Event\Subscriber\_DoctrineSubscriber_;
use App\Manager\PersonaManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class PersonaDoctrineSubscriber extends _DoctrineSubscriber_
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Persona)
            return;

        $this->personaHash($object);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

        $object = $args->getObject();

        if (!$object instanceof Persona)
            return;

        $this->personaHash($object);
    }

    private function personaHash(Persona $object)
    {
        /** @var PersonaManager $personaManager */
        $personaManager = $this->get('app.manager.persona');

        $object->setHash($personaManager->generarHash($object));
    }
}
