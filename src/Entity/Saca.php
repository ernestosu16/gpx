<?php

namespace App\Entity;

use App\Repository\SacaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: SacaRepository::class)]
class Saca extends _Entity_
{

    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    private string $sello;

    #[ORM\Column(type: 'datetime')]
    private $fecha_creacion;

    #[ORM\Column(type: 'string', length: 31)]
    private $codigo;

    #[ORM\Column(type: 'float')]
    private $peso;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $estado;

    #[ORM\ManyToOne(targetEntity: Factura::class, inversedBy: 'sacas')]
    private $factura;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $origen;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $destino;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $tipo_embalaje;

    #[ORM\ManyToOne(targetEntity: Saca::class, inversedBy: 'sacas_children')]
    private $saca_colectora;

    #[ORM\OneToMany(mappedBy: 'saca_colectora', targetEntity: Saca::class)]
    private $sacas_children;

    #[Pure]
    public function __construct()
    {
        $this->sacas_children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSello(): string
    {
        return $this->sello;
    }

    /**
     * @param string $sello
     */
    public function setSello(string $sello): void
    {
        $this->sello = $sello;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function setPeso(float $peso): self
    {
        $this->peso = $peso;

        return $this;
    }

    public function getEstado(): ?Nomenclador
    {
        return $this->estado;
    }

    public function setEstado(?Nomenclador $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFactura(): ?Factura
    {
        return $this->factura;
    }

    public function setFactura(?Factura $factura): self
    {
        $this->factura = $factura;

        return $this;
    }

    public function getOrigen(): ?Estructura
    {
        return $this->origen;
    }

    public function setOrigen(?Estructura $origen): self
    {
        $this->origen = $origen;

        return $this;
    }

    public function getDestino(): ?Estructura
    {
        return $this->destino;
    }

    public function setDestino(?Estructura $destino): self
    {
        $this->destino = $destino;

        return $this;
    }

    public function getTipoEmbalaje(): ?Nomenclador
    {
        return $this->tipo_embalaje;
    }

    public function setTipoEmbalaje(?Nomenclador $tipo_embalaje): self
    {
        $this->tipo_embalaje = $tipo_embalaje;

        return $this;
    }

    public function getSacaColectora(): ?self
    {
        return $this->saca_colectora;
    }

    public function setSacaColectora(?self $saca_colectora): self
    {
        $this->saca_colectora = $saca_colectora;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSacasChildren(): Collection
    {
        return $this->sacas_children;
    }

    public function addSacasChild(self $sacasChild): self
    {
        if (!$this->sacas_children->contains($sacasChild)) {
            $this->sacas_children[] = $sacasChild;
            $sacasChild->setSacaColectora($this);
        }

        return $this;
    }

    public function removeSacasChild(self $sacasChild): self
    {
        if ($this->sacas_children->removeElement($sacasChild)) {
            // set the owning side to null (unless already changed)
            if ($sacasChild->getSacaColectora() === $this) {
                $sacasChild->setSacaColectora(null);
            }
        }

        return $this;
    }




}
