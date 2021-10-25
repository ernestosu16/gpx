<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

abstract class _Entity_
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[Groups(['default'])]
    protected ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }
}
