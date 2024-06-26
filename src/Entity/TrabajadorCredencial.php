<?php

namespace App\Entity;

use App\Repository\TrabajadorCredencialRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'app_trabajador_credencial')]
#[ORM\Entity(repositoryClass: TrabajadorCredencialRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_USUARIO', fields: ['usuario'])]
#[ORM\HasLifecycleCallbacks]
class TrabajadorCredencial implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'credencial', targetEntity: Trabajador::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Trabajador $trabajador = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $usuario = null;

    #[ORM\Column(type: 'string', length: 60)]
    private ?string $contrasena = null;

    #[ORM\Column(name: 'es_admin', type: 'boolean')]
    private bool $admin = false;

    #[ORM\Column(type: 'text', length: 120, nullable: false)]
    private string $navegador = '';

    #[ORM\Column(type: 'datetime', options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTime $creado;

    #[ORM\Column(type: 'json')]
    private array $ultima_conexion = [];

    #[ORM\Column(type: 'datetime', options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTime $ultimo_acceso;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $forzar_cambio_contrasena = false;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private ?string $salt = null;

    public function __construct()
    {
        $this->creado = new DateTime();
        $this->ultimo_acceso = new DateTime();
    }

    public function __toString(): string
    {
        return $this->usuario;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getContrasena(): ?string
    {
        return $this->contrasena;
    }

    public function setContrasena(?string $contrasena): self
    {
        if ($contrasena)
            $this->contrasena = $contrasena;

        return $this;
    }

    public function getAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): TrabajadorCredencial
    {
        $this->admin = $admin;
        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function getNavegador(): string
    {
        return $this->navegador;
    }

    public function setNavegador(string $navegador): TrabajadorCredencial
    {
        $this->navegador = $navegador;
        return $this;
    }

    public function getCreado(): DateTime
    {
        return $this->creado;
    }

    public function setCreado(DateTime $creado): TrabajadorCredencial
    {
        $this->creado = $creado;
        return $this;
    }

    public function getUltimoAcceso(): DateTime
    {
        return $this->ultimo_acceso;
    }

    public function setUltimoAcceso(DateTime $ultimo_acceso): TrabajadorCredencial
    {
        $this->ultimo_acceso = $ultimo_acceso;
        return $this;
    }

    public function getUltimaConexion(): array
    {
        return $this->ultima_conexion;
    }

    public function setUltimaConexion(array $ultima_conexion): TrabajadorCredencial
    {
        $this->ultima_conexion = $ultima_conexion;
        return $this;
    }

    public function getForzarCambioContrasena(): bool
    {
        return $this->forzar_cambio_contrasena;
    }

    public function setForzarCambioContrasena(bool $forzar_cambio_contrasena): TrabajadorCredencial
    {
        $this->forzar_cambio_contrasena = $forzar_cambio_contrasena;
        return $this;
    }

    public function getTrabajador(): ?Trabajador
    {
        return $this->trabajador;
    }

    public function setTrabajador(?Trabajador $trabajador): self
    {
        $this->trabajador = $trabajador;

        return $this;
    }

    #[Pure] public function getPersona(): ?Persona
    {
        return $this->getTrabajador()?->getPersona();
    }

    #[Pure] public function getNombre(): ?string
    {
        return $this->getPersona()?->getNombre();
    }

    #[Pure] public function getCargo(): ?string
    {
        return $this->getTrabajador()?->getCargo();
    }

    #[Pure] public function getEstructura(): ?Estructura
    {
        return $this->getTrabajador()?->getEstructura();
    }

    #[Pure] public function getUsername(): ?string
    {
        return $this->usuario;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->contrasena;
    }

    #[Pure] public function getUserIdentifier(): string
    {
        return $this->getUsuario();
    }

    #[Pure] public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->isAdmin())
            $roles[] = 'ROLE_ADMIN';

        return $roles;
    }

    public function setSalt(string $salt): static
    {
        $this->salt = $salt;
        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function eraseCredentials()
    {
        return null;
    }

    #[Pure] public function getHabilitado(): ?bool
    {
        return $this->getTrabajador()?->getHabilitado();
    }
}
