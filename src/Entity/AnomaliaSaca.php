<?php

namespace App\Entity;

use App\Repository\AnomaliaSacaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnomaliaSacaRepository::class)]
class AnomaliaSaca extends Anomalia
{

    #[ORM\ManyToOne(targetEntity: Saca::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $saca;

    public function getSaca(): ?Saca
    {
        return $this->saca;
    }

    public function setSaca(?Saca $saca): self
    {
        $this->saca = $saca;

        return $this;
    }
}
