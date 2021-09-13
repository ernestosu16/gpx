<?php

namespace App\Event\Subscriber;

use App\Entity\Persona;
use App\Manager\PersonaManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PersonaSubscriber extends _Subscriber_
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

        if (!$object instanceof Persona)
            return;

        /** @var PersonaManager $personaManager */
        $personaManager = $this->get('app.manager.persona');

        $object->setHash($personaManager->generarHash($object));
    }
}
