<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\TrabajadorCredencial;
use App\Event\Subscriber\_DoctrineSubscriber_;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
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
        if (!$args->getObject() instanceof TrabajadorCredencial)
            return;

        $this->passwordEncrypt($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        if (!$args->getObject() instanceof TrabajadorCredencial)
            return;

        $this->passwordEncrypt($args);
    }

    private function passwordEncrypt(LifecycleEventArgs $args): void
    {
        /** @var UnitOfWork $uow */
        $uow = $args->getObjectManager()->getUnitOfWork();
        $uow->computeChangeSets(); // do not compute changes if inside a listener
        $changeSet = $uow->getEntityChangeSet($args->getObject()); // view column change

        /** @var TrabajadorCredencial $object */
        $object = $args->getObject();
        if (isset($changeSet['contrasena']) && $object->getContrasena()) {
            $object->setContrasena($this->passwordHasher->hashPassword($object, $object->getContrasena()));
        }
    }
}
