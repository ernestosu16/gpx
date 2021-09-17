<?php

namespace App\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class _Menu_
{
    public function __construct(
        private FactoryInterface       $factory,
        private EntityManagerInterface $entityManager,
        private ContainerInterface     $container
    )
    {
    }

    public function get(string $id, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        return $this->container->get($id, $invalidBehavior);
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
