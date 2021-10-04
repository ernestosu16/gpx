<?php

namespace App\Entity;

use App\Repository\TrabajadorCredencialRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: TrabajadorCredencialRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_USUARIO', fields: ['usuario'])]
class TrabajadorCredencial implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'credencial', targetEntity: Trabajador::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Trabajador $trabajador = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $usuario = null;

    #[ORM\Column(type: 'string', length: 60, nullable: false)]
    private string $contrasena;

    #[ORM\Column(name: 'es_admin', type: 'boolean')]
    private bool $admin = false;

    #[ORM\Column(type: 'string', length: 120)]
    private string $navegador;

    #[ORM\Column(type: 'json')]
    private array $ultima_conexion;

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

    public function getContrasena(): string
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

    public function getUltimaConexion(): array
    {
        return $this->ultima_conexion;
    }

    public function setUltimaConexion(array $ultima_conexion): TrabajadorCredencial
    {
        $this->ultima_conexion = $ultima_conexion;
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

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }


}
