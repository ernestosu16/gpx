<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu extends Nomenclador
{
    public function getRoute(): ?string
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

    /**
     * @throws Exception
     */
    public function getNotify(): ?DateTime
    {
        $v = $this->getParametro('notify');

        return $v ? new DateTime($v) : null;
    }

    public function setNotify(?DateTime $v): Menu
    {
        $this->setParametro('notify', $v?->format('Y-m-d'));

        return $this;
    }

    /**
     * @throws Exception
     */
    public function checkNotify(): bool
    {
        $notify = $this->getNotify();

        if (!$notify)
            return false;

        return $notify >= new DateTime();
    }
}
