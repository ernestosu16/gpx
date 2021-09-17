<?php

namespace App\Event\Subscriber;

use App\Entity\Persona;
use App\Manager\PersonaManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class PersonaSubscriber extends _Subscriber_
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
