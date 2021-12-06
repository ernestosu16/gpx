<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\TrabajadorCredencial;
use App\Event\Subscriber\_DoctrineSubscriber_;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class TrabajadorCredencialDoctrineSubscriber extends _DoctrineSubscriber_
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
        /** @var TrabajadorCredencial $object */
        $object = $args->getObject();
        if (!$object instanceof TrabajadorCredencial)
            return;

        $this->passwordEncrypt($object);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        /** @var TrabajadorCredencial $object */
        $object = $args->getObject();
        if (!$object instanceof TrabajadorCredencial)
            return;

        $changeSet = $args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($object);
        if (isset($changeSet['contrasena']))
            $this->passwordEncrypt($object);
    }

    private function passwordEncrypt(TrabajadorCredencial $credencial): void
    {
        if (!$credencial->getContrasena())
            return;

        $credencial->setContrasena($this->passwordHasher->hashPassword($credencial, $credencial->getContrasena()));
    }
}
