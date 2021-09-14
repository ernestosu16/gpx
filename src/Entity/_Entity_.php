<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class _Entity_
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    protected ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }
}
