<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\TrabajadorCredencial;
use App\Event\Subscriber\_Subscriber_;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TrabajadorCredencialSubscriber extends _Subscriber_
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ContainerInterface $container, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($container);
        $this->passwordHasher = $passwordHasher;
    }

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

        if (!$object instanceof TrabajadorCredencial)
            return;

        $this->passwordEncrypt($object);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

        $object = $args->getObject();

        if (!$object instanceof TrabajadorCredencial)
            return;

        $this->passwordEncrypt($object);
    }

    private function passwordEncrypt(TrabajadorCredencial $object)
    {
        $hashedPassword = $this->passwordHasher->hashPassword($object, $object->getContrasena());
        $object->setContrasena($hashedPassword);
    }
}
