<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\TrabajadorCredencial;
use App\Event\Subscriber\_Subscriber_;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class TrabajadorCredencialSubscriber extends _Subscriber_
{
    private UserPasswordHasherInterface $passwordHasher;

    #[Pure] public function __construct(ContainerInterface $container, UserPasswordHasherInterface $passwordHasher)
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

    private function passwordEncrypt(TrabajadorCredencial $object): void
    {
        if ($object->getContrasena())
            $object->setContrasena($this->passwordHasher->hashPassword($object, $object->getContrasena()));
    }
}
