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

    #[ORM\Column(type: 'string')]
    private string $salt;

    public function __construct()
    {
        $this->salt = '';
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

    public function getRoles(): array
    {
        $roles = [];
        /** @var Grupo[] $grupos */
        $grupos = $this->getTrabajador()->getGrupos();

        foreach ($grupos as $grupo) {
            $roles = array_merge($roles, $grupo->getRoles());
        }

        return $roles;
    }

    public function setSalt(string $salt): self
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
        // TODO: Implement eraseCredentials() method.
    }


}
