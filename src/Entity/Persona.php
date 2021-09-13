<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity]
#[ORM\Index(fields: ['numero_identidad'], name: 'IDX_NUMERO_IDENTIDAD')]
class Persona extends _Entity_
{
    #[ORM\Column(type: 'string', length: 11, unique: false)]
    private string $numero_identidad;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombre_primero;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nombre_segundo;

    #[ORM\Column(type: 'string', length: 100)]
    private string $apellido_primero;

    #[ORM\Column(type: 'string', length: 100)]
    private string $apellido_segundo;

    #[Pure] public function __toString(): string
    {
        return $this->getNombreCompleto();
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

    /**
     * @return string
     */
    public function getNombrePrimero(): string
    {
        return $this->nombre_primero;
    }

    /**
     * @param string $nombre_primero
     * @return Persona
     */
    public function setNombrePrimero(string $nombre_primero): Persona
    {
        $this->nombre_primero = $nombre_primero;
        return $this;
    }

    /**
     * @return string
     */
    public function getNombreSegundo(): string
    {
        return $this->nombre_segundo;
    }

    /**
     * @param string $nombre_segundo
     * @return Persona
     */
    public function setNombreSegundo(string $nombre_segundo): Persona
    {
        $this->nombre_segundo = $nombre_segundo;
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
