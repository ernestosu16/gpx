<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


abstract class Anomalia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $descripcion;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $anomalia;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getAnomalia(): ?Nomenclador
    {
        return $this->anomalia;
    }

    public function setAnomalia(?Nomenclador $anomalia): self
    {
        $this->anomalia = $anomalia;

        return $this;
    }
}