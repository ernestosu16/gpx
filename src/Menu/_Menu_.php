<?php

namespace App\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class _Menu_
{
    protected ContainerInterface $container;
    protected FactoryInterface $factory;
    protected EntityManagerInterface $entityManager;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

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

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function get(string $id, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        return $this->container->get($id, $invalidBehavior);
    }
}
