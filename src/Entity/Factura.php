<?php

namespace App\Entity;

use App\Repository\FacturaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturaRepository::class)]
class Factura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $fecha;

    #[ORM\Column(type: 'string', length: 50)]
    private $numero_factura;

    #[ORM\ManyToOne(targetEntity: Trabajador::class)]
    private $chofer;

    #[ORM\Column(type: 'string', length: 7, nullable: true)]
    private $chapa_vehiculo;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $estado;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    private $tipo_vehiculo;

    #[ORM\ManyToOne(targetEntity: Trabajador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $trabajador;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    private $destino;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    private $origen;

    #[ORM\OneToMany(mappedBy: 'factura', targetEntity: Saca::class)]
    private $sacas;

    #[ORM\ManyToMany(targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'factura_anomalia_asignada')]
    private ?Collection $anomalias;

    #[ORM\Column(type: 'json', nullable: true)]
    private $observaciones;


    public function __construct()
    {
        $this->sacas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|null
     */
    public function getAnomalias(): ?Collection
    {
        return $this->anomalias;
    }

    /**
     * @param Collection|null $anomalias
     * @return Factura
     */
    public function setAnomalias(?Collection $anomalias): Factura
    {
        $this->anomalias = $anomalias;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param mixed $observaciones
     * @return Factura
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
        return $this;
    }
}
