<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity]
class Trabajador extends _Entity_
{
    #[ORM\OneToOne(targetEntity: Persona::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'persona_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Persona $persona;

    #[ORM\OneToOne(mappedBy: 'trabajador', targetEntity: Usuario::class, cascade: ['persist'])]
    private ?Usuario $usuario;

    #[ORM\ManyToMany(targetEntity: Nomenclador::class)]
    #[ORM\JoinTable(name: 'trabajador_grupo_asignado')]
    #[ORM\JoinColumn(name: 'trabajador_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'grupo_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $grupos;

    #[ORM\Column(type: 'string', length: 11)]
    private string $cargo;

    #[ORM\Column(type: 'boolean')]
    private bool $habilitado = true;

    #[Pure] public function __construct()
    {
        $this->grupos = new ArrayCollection();
    }

    public function getCargo(): ?string
    {
        return $this->cargo;
    }

    public function setCargo(string $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }

    public function getHabilitado(): ?bool
    {
        return $this->habilitado;
    }

    public function setHabilitado(bool $habilitado): self
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): self
    {
        $this->persona = $persona;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        // unset the owning side of the relation if necessary
        if ($usuario === null && $this->usuario !== null) {
            $this->usuario->setTrabajador(null);
        }

        // set the owning side of the relation if necessary
        if ($usuario !== null && $usuario->getTrabajador() !== $this) {
            $usuario->setTrabajador($this);
        }

        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGrupos(): Collection
    {
        return $this->grupos;
    }

    public function addGrupo(Nomenclador $grupo): self
    {
        if (!$this->grupos->contains($grupo)) {
            $this->grupos[] = $grupo;
        }

        return $this;
    }

    public function removeGrupo(Nomenclador $grupo): self
    {
        $this->grupos->removeElement($grupo);

        return $this;
    }
}
