<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu extends Nomenclador
{
    public function getRoute(): string
    {
        return $this->getParametro('route');
    }

    public function setRoute(?string $v): Menu
    {
        $this->setParametro('route', $v);

        return $this;
    }

    public function getClass(): string
    {
        return $this->getParametro('class');
    }

    public function setClass(?string $v): Menu
    {
        $this->setParametro('class', $v);

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->getParametro('icon');
    }

    public function setIcon(?string $v): Menu
    {
        $this->setParametro('icon', $v);

        return $this;
    }
}
