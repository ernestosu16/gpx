<?php

namespace App\Manager;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class RouteManager extends _Manager_
{
    private RouterInterface $route;

    public function setRoute(RouterInterface $router)
    {
        $this->route = $router;
    }

    public function find(string $id): ?Route
    {
        return $this->route->getRouteCollection()->get($id);
    }

    public function findAll(): array
    {
        return $this->route->getRouteCollection()->all();
    }

    public function findOneByPatch($path): ?Route
    {
        /** @var Route $route */
        foreach ($this->findAll() as $route) {
            if ($path === $route->getPath())
                return $route;
        }
        return null;
    }

    public function lookForTheKeyOfARoute(?string $path): int|string|null
    {
        if ($path)
            /** @var Route $route */
            foreach ($this->findAll() as $key => $route) {
                if ($path === $route->getPath())
                    return $key;
            }
        return null;
    }
}
