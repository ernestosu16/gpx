<?php

namespace App\Entity;

use App\Repository\PersonaRepository;
use App\Util\RegexUtil;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: PersonaRepository::class)]
#[ORM\Index(fields: ['numero_identidad'], name: 'IDX_NUMERO_IDENTIDAD')]
class Persona extends _Entity_
{
    #[ORM\Column(type: 'string', length: 32, unique: true)]
    private string $hash;

    #[ORM\Column(type: 'string', length: 11, nullable: true)]
    #[Assert\Regex(
        pattern : RegexUtil::NUMERO_IDENTIDAD,
        message : 'Número de identidad es incorrecto')]
    #[Assert\Length(min : 11, max : 11)]
    private ?string $numero_identidad;

    #[ORM\Column(type: 'string', length: 11, nullable: true)]
    private ?string $numero_pasaporte;

    #[ORM\Column(type: 'string', length: 50)]
    private string $nombre_primero;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $nombre_segundo;

    #[ORM\Column(type: 'string', length: 50)]
    private string $apellido_primero;

    #[ORM\Column(type: 'string', length: 50)]
    private string $apellido_segundo;

    #[ORM\Column(type: 'boolean')]
    private bool $esExtranjero = false;

    #[ORM\Column(type: 'boolean')]
    private bool $esValido = false;

    #[Pure] public function __toString(): string
    {
        return $this->getNombreCompleto();
    }

    public function __construct()
    {
        $this->numero_pasaporte = null;
        $this->nombre_segundo = null;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getNumeroIdentidad(): ?string
    {
        return $this->numero_identidad;
    }

    public function setNumeroIdentidad(?string $numero_identidad): self
    {
        $this->numero_identidad = $numero_identidad;

        return $this;
    }

    public function getNumeroPasaporte(): ?string
    {
        return $this->numero_pasaporte;
    }

    public function setNumeroPasaporte(?string $numero_pasaporte): self
    {
        $this->numero_pasaporte = $numero_pasaporte;

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
     * @return string|null
     */
    public function getNombreSegundo(): ?string
    {
        return $this->nombre_segundo;
    }

    /**
     * @param string|null $nombre_segundo
     * @return Persona
     */
    public function setNombreSegundo(?string $nombre_segundo): Persona
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

    public function getApellidoSegundo(): string
    {
        return $this->apellido_segundo;
    }

    public function setApellidoSegundo(string $apellido_segundo): self
    {
        $this->apellido_segundo = $apellido_segundo;

        return $this;
    }

    public function getEsExtranjero(): bool
    {
        return $this->esExtranjero;
    }

    public function setEsExtranjero(bool $esExtranjero): self
    {
        $this->esExtranjero = $esExtranjero;

        return $this;
    }

    public function getEsValido(): bool
    {
        return $this->esValido;
    }

    public function setEsValido(bool $esValido): self
    {
        $this->esValido = $esValido;

        return $this;
    }

    #[Pure] public function getNombre(): string
    {
        return $this->getNombrePrimero() . ' ' . $this->getNombreSegundo();
    }

    #[Pure] public function getApellidos(): string
    {
        return $this->getApellidoPrimero() . ' ' . $this->getApellidoSegundo();
    }

    #[Pure] public function getNombreCompleto(): string
    {
        return $this->getNombre() . ' ' . $this->getApellidos();
    }
}
