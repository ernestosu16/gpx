<?php

namespace App\Entity;

use App\Entity\Traits\VersionTrait;
use App\Repository\PaisRepository;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: PaisRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IATA', columns: ['iata'])]
class Pais extends _Entity_
{
    use VersionTrait;

    #[ORM\Column(type: 'string', length: 45)]
    private string $nombre;

    #[ORM\Column(type: 'string', length: 2)]
    private string $iata;

    #[ORM\Column(type: 'string', length: 3, unique: true, nullable: true)]
    private ?string $codigo_aduana;

    #[ORM\Column(type: 'boolean')]
    private bool $habilitado;

    public function __construct()
    {
        $this->habilitado = true;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getIata(): string
    {
        return $this->iata;
    }

    public function setIata(string $iata): self
    {
        $this->iata = u($iata)->upper();
        return $this;
    }

    function getHabilitado(): bool
    {
        return $this->habilitado;
    }

    public function isHabilitado(): bool
    {
        return $this->habilitado;
    }

    public function setHabilitado(bool $habilitado): self
    {
        $this->habilitado = $habilitado;
        return $this;
    }
}
