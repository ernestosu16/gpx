<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'UNQ_USUARIO', fields: ['usuario'])]
class TrabajadorCredencial implements UserInterface
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'credencial', targetEntity: Trabajador::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Trabajador $trabajador = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $usuario = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $contrasena = null;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $session;

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): self
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
        $this->contrasena = $contrasena;

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

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(?string $session): TrabajadorCredencial
    {
        $this->session = $session;
        return $this;
    }

    #[Pure] public function getUsername(): ?string
    {
        return $this->getUsuario();
    }

    #[Pure] public function getPassword(): ?string
    {
        return $this->getContrasena();
    }


    #[Pure] public function getUserIdentifier(): string
    {
        return $this->getUsuario();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}
