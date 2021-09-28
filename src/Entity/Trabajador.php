<?php

namespace App\Entity;

use App\Repository\TrabajadorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrabajadorRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_PERSONA_ID', columns: ['persona_id'])]
#[ORM\Index(columns: ['estructura_id'], name: 'IDX_ESTRUCTURA_ID')]
class Trabajador extends _Entity_
{
    #[ORM\OneToOne(targetEntity: Persona::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'persona_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Assert\Valid]
    private Persona $persona;

    #[ORM\OneToOne(mappedBy: 'trabajador', targetEntity: TrabajadorCredencial::class, cascade: ['persist'])]
    #[Assert\Valid]
    private ?TrabajadorCredencial $credencial = null;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Estructura $estructura;

    #[ORM\ManyToMany(targetEntity: Nomenclador::class)]
    #[ORM\JoinTable(name: 'trabajador_grupo_asignado')]
    #[ORM\JoinColumn(name: 'trabajador_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'grupo_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Assert\Valid]
    private Collection $grupos;

    #[ORM\Column(type: 'string', length: 100)]
    private string $cargo;

    #[ORM\Column(type: 'boolean')]
    private bool $habilitado = true;

    #[Pure] public function __construct()
    {
        $this->persona = new Persona();
        $this->grupos = new ArrayCollection();
    }

    #[Pure] public function __toString(): string
    {
        return (string)'';
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

    public function getCredencial(): ?TrabajadorCredencial
    {
        return $this->credencial;
    }

    public function setCredencial(?TrabajadorCredencial $credencial): self
    {
        // unset the owning side of the relation if necessary
        if ($credencial === null && $this->credencial !== null) {
            $this->credencial->setTrabajador(null);
        }

        // set the owning side of the relation if necessary
        if ($credencial !== null && $credencial->getTrabajador() !== $this) {
            $credencial->setTrabajador($this);
        }

        $this->credencial = $credencial;

        return $this;
    }

    public function getEstructura(): ?Estructura
    {
        return $this->estructura;
    }

    public function setEstructura(?Estructura $estructura): self
    {
        $this->estructura = $estructura;

        return $this;
    }

    /**
     * @return Collection|Grupo[]
     */
    public function getGrupos(): Collection
    {
        return $this->grupos;
    }

    public function addGrupo(Grupo $grupo): self
    {
        if (!$this->grupos->contains($grupo)) {
            $this->grupos[] = $grupo;
        }

        return $this;
    }

    public function removeGrupo(Grupo $grupo): self
    {
        $this->grupos->removeElement($grupo);

        return $this;
    }

    #[Pure] public function getNombre(): string
    {
        return $this->getPersona()->getNombre();
    }

    public function setDatoCredencial($username, $password): self
    {
        $credencial = new TrabajadorCredencial();
        $credencial->setUsuario($username);
        $credencial->setContrasena($password);

        $this->setCredencial($credencial);
        return $this;
    }

    public function setDatoPersona(
        string $numeroIdentidad,
        string $nombrePrimero,
        string $nombreSegundo,
        string $apellidoPrimero,
        string $apellidoSegundo,
        bool   $esExtranjero = false,
    ): self
    {
        $persona = new Persona();

        if ($esExtranjero)
            $persona->setNumeroPasaporte($numeroIdentidad);
        else
            $persona->setNumeroIdentidad($numeroIdentidad);
        $persona->setNombrePrimero($nombrePrimero);
        $persona->setNombreSegundo($nombreSegundo);
        $persona->setApellidoPrimero($apellidoPrimero);
        $persona->setApellidoSegundo($apellidoSegundo);
        $persona->setEsExtranjero($esExtranjero);

        $this->setPersona($persona);
        return $this;
    }
}
