<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity]
#[ORM\Index(fields: ['numero_identidad'], name: 'IDX_NUMERO_IDENTIDAD')]
class Persona extends _Entity_
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36)]
    #[ORM\GeneratedValue(strategy: "UUID")]
    private string $id;

    #[ORM\Column(type: "string", length: 11, unique: true)]
    private string $numero_identidad;

    #[ORM\Column(type: "string", length: 100)]
    private string $nombre;

    #[ORM\Column(type: "string", length: 100)]
    private string $apellido_primero;

    #[ORM\Column(type: "string", length: 100)]
    private string $apellido_segundo;

    #[Pure] public function __toString(): string
    {
        return $this->getNombreCompleto();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNumeroIdentidad(): ?string
    {
        return $this->numero_identidad;
    }

    public function setNumeroIdentidad(string $numero_identidad): self
    {
        $this->numero_identidad = $numero_identidad;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidoPrimero(): ?string
    {
        return $this->apellido_primero;
    }

    public function setApellidoPrimero(string $apellido_primero): self
    {
        $this->apellido_primero = $apellido_primero;

        return $this;
    }

    public function getApellidoSegundo(): ?string
    {
        return $this->apellido_segundo;
    }

    public function setApellidoSegundo(string $apellido_segundo): self
    {
        $this->apellido_segundo = $apellido_segundo;

        return $this;
    }

    public function getNombreCompleto(): string
    {
        return sprintf('%s %s %s', $this->nombre, $this->apellido_primero, $this->apellido_segundo);
    }
}
