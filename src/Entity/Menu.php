<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu extends Nomenclador
{
    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->getParametro('route');
    }

    /**
     * @param string|null $href
     * @return Menu
     */
    public function setRoute(?string $href): Menu
    {
        $this->setParametro('route', $href);

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->getParametro('class');
    }

    /**
     * @param string|null $href
     * @return Menu
     */
    public function setClass(?string $href): Menu
    {
        $this->setParametro('class', $href);

        return $this;
    }
}
