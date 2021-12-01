<?php

namespace App\Entity;

use App\Repository\TrabajadorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_trabajador')]
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
    #[ORM\JoinTable(name: 'app_trabajador_grupo_asignado')]
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

    #[Pure] public function getNombre(): string
    {
        return $this->getPersona()->getNombre();
    }

    #[Pure] public function getNombreCompleto(): string
    {
        return $this->getPersona()->getNombreCompleto();
    }

    public function getSexo(): string
    {
        return $this->getPersona()->getSexo();
    }

    public function getNacimiento(): DateTime
    {
        return $this->getPersona()->getNacimiento();
    }

    public function setDatoCredencial($username, $password, bool $isAdmin = false): self
    {
        $credencial = new TrabajadorCredencial();
        $credencial->setUsuario($username);
        $credencial->setContrasena($password);
        $credencial->setAdmin($isAdmin);

        $this->setCredencial($credencial);
        return $this;
    }

    public function setDatoPersona(
        string $numeroID,
        string $nombrePrimero,
        string $nombreSegundo,
        string $apellidoPrimero,
        string $apellidoSegundo,
        Pais   $pais,
    ): Trabajador
    {
        $persona = new Persona();

        if ($pais->getCodigoAduana() === Pais::PRINCIPAL)
            $persona->setNumeroIdentidad($numeroID);
        else
            $persona->setNumeroPasaporte($numeroID);

        $persona->setNombrePrimero($nombrePrimero);
        $persona->setNombreSegundo($nombreSegundo);
        $persona->setApellidoPrimero($apellidoPrimero);
        $persona->setApellidoSegundo($apellidoSegundo);
        $persona->setPais($pais);

        $this->setPersona($persona);

        return $this;
    }

    public function getMenus(): Collection
    {
        $menus = [];
        foreach ($this->getGrupos() as $grupo) {
            $menus = array_merge($menus, $grupo->getMenus()->toArray());
        }
        return new ArrayCollection($menus);
    }

    #[Pure] public function hasCredentials(): bool
    {
        return $this->credencial && $this->credencial->getUsuario();
    }
}
