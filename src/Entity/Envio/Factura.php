<?php

namespace App\Entity\Envio;

use App\Entity\_Entity_;
use App\Entity\Estructura;
use App\Entity\Nomenclador;
use App\Entity\Trabajador;
use App\Repository\FacturaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturaRepository::class)]
class Factura extends _Entity_
{
    #[ORM\Column(type: 'datetime')]
    private $fecha;

    #[ORM\Column(type: 'string', length: 50)]
    private $numero_factura;

    #[ORM\Column(type: 'string', length: 50)]
    private $codigo_factura;

    #[ORM\ManyToOne(targetEntity: Trabajador::class)]
    private $chofer;

    #[ORM\Column(type: 'string', length: 7, nullable: true)]
    private $chapa_vehiculo;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $estado;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class, cascade: ['persist'])]
    private $tipo_vehiculo;

    #[ORM\ManyToOne(targetEntity: Trabajador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $trabajador;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    private $destino;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    private $origen;

    #[ORM\OneToMany(mappedBy: 'factura', targetEntity: Saca::class, cascade: ['persist'])]
    private $sacas;

    public function __construct()
    {
        $this->sacas = new ArrayCollection();
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getNumeroFactura(): ?string
    {
        return $this->numero_factura;
    }

    public function setNumeroFactura(string $numero_factura): self
    {
        $this->numero_factura = $numero_factura;

        return $this;
    }

    public function getChofer(): ?Trabajador
    {
        return $this->chofer;
    }

    public function setChofer(?Trabajador $chofer): self
    {
        $this->chofer = $chofer;

        return $this;
    }

    public function getChapaVehiculo(): ?string
    {
        return $this->chapa_vehiculo;
    }

    public function setChapaVehiculo(?string $chapa_vehiculo): self
    {
        $this->chapa_vehiculo = $chapa_vehiculo;

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

    public function getTipoVehiculo(): ?Nomenclador
    {
        return $this->tipo_vehiculo;
    }

    public function setTipoVehiculo(?Nomenclador $tipo_vehiculo): self
    {
        $this->tipo_vehiculo = $tipo_vehiculo;

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

    public function getDestino(): ?Estructura
    {
        return $this->destino;
    }

    public function setDestino(?Estructura $destino): self
    {
        $this->destino = $destino;

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

    /**
     * @return Collection|Saca[]
     */
    public function getSacas(): Collection
    {
        return $this->sacas;
    }

    public function addSaca(Saca $saca): self
    {
        if (!$this->sacas->contains($saca)) {
            $this->sacas[] = $saca;
            $saca->setFactura($this);
        }

        return $this;
    }

    public function removeSaca(Saca $saca): self
    {
        if ($this->sacas->removeElement($saca)) {
            // set the owning side to null (unless already changed)
            if ($saca->getFactura() === $this) {
                $saca->setFactura(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoFactura()
    {
        return $this->codigo_factura;
    }

    /**
     * @param mixed $codigo_factura
     * @return Factura
     */
    public function setCodigoFactura($codigo_factura)
    {
        $this->codigo_factura = $codigo_factura;
        return $this;
    }




}
