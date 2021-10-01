<?php

namespace App\Entity;

use App\Repository\EstructuraTipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: EstructuraTipoRepository::class)]
#[ORM\Cache]
class EstructuraTipo extends Nomenclador
{
    #[ORM\ManyToMany(targetEntity: Estructura::class, mappedBy: 'tipos')]
    #[ORM\JoinTable(name: 'estructura_tipo_asignado')]
    private Collection $estructuras;

    #[Pure] public function __construct()
    {
        parent::__construct();
        $this->estructuras = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getEstructuras(): Collection
    {
        return $this->estructuras;
    }

    public function addEstructura(Estructura $estructura): self
    {
        if (!$this->estructuras->contains($estructura)) {
            $this->estructuras[] = $estructura;
            $estructura->addTipo($this);
        }

        return $this;
    }

    public function removeEstructura(Estructura $estructura): self
    {
        if ($this->estructuras->removeElement($estructura)) {
            $estructura->removeTipo($this);
        }

        return $this;
    }
}
