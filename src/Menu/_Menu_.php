<?php

namespace App\Menu;

use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use App\Manager\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class _Menu_
{
    protected RouteManager $routeManager;
    protected FactoryInterface $factory;
    protected EntityManagerInterface $entityManager;
    protected RequestStack $requestStack;
    protected TokenStorageInterface $tokenStorage;
    protected LoggerInterface $logger;

    public function setRouteManager(RouteManager $routeManager): void
    {
        $this->routeManager = $routeManager;
    }

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

    public function setStorage(TokenStorageInterface $tokenStorage): _Menu_
    {
        $this->tokenStorage = $tokenStorage;
        return $this;
    }

    protected function getCredencial(): TrabajadorCredencial|UserInterface
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    protected function getTrabajador(): ?Trabajador
    {
        return $this->getCredencial()->getTrabajador();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
