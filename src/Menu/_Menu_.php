<?php

namespace App\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class _Menu_
{
    protected FactoryInterface $factory;
    protected EntityManagerInterface $entityManager;
    protected RequestStack $requestStack;
    protected TokenStorageInterface $storage;

    /**
     * @param FactoryInterface $factory
     */
    public function setFactory(FactoryInterface $factory): void
    {
        $this->factory = $factory;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function setRequestStack(RequestStack $requestStack): _Menu_
    {
        $this->requestStack = $requestStack;
        return $this;
    }

    public function setStorage(TokenStorageInterface $storage): _Menu_
    {
        $this->storage = $storage;
        return $this;
    }
}
