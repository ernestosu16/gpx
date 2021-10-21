<?php

namespace App\Entity;

use App\Repository\EnvioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvioRepository::class)]
class Envio extends _Entity_
{
    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    private string $cod_tracking;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $peso;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'estructura_destino_id', referencedColumnName: 'id', nullable: true)]
    private ?Estructura $estructura_destino;

    // cambiar Nomenclador por Estado cuando se cree
    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

    // cambiar Nomenclador por saca cuando se cree
    #[ORM\ManyToOne(targetEntity: Saca::class)]
    #[ORM\JoinColumn(name: 'saca_id', referencedColumnName: 'id', nullable: true)]
    private ?Saca $saca;

    #[ORM\ManyToOne(targetEntity: Factura::class)]
    #[ORM\JoinColumn(name: 'factura_id', referencedColumnName: 'id', nullable: true)]
    private ?Factura $factura;

    public function __toString(): string
    {
        return $this->cod_tracking;
    }

    public function getCodTracking(): string
    {
        return $this->cod_tracking;
    }

    public function setCodTracking(string $cod_tracking): void
    {
        $this->cod_tracking = $cod_tracking;
    }

    public function getPeso(): float
    {
        return $this->peso;
    }

    public function setPeso(float $peso): void
    {
        $this->peso = $peso;
    }

    public function getEstructuraDestino(): ?Estructura
    {
        return $this->estructura_destino;
    }

    public function setEstructuraDestino(?Estructura $estructura_destino): void
    {
        $this->estructura_destino = $estructura_destino;
    }

    public function getEstado(): Nomenclador
    {
        return $this->estado;
    }

    public function setEstado(Nomenclador $estado): void
    {
        $this->estado = $estado;
    }

    public function getSaca(): ?Saca
    {
        return $this->saca;
    }

    public function setSaca(?Saca $saca): void
    {
        $this->saca = $saca;
    }

    public function getFactura(): ?Factura
    {
        return $this->factura;
    }


    public function setFactura(?Factura $factura): Envio
    {
        $this->factura = $factura;
        return $this;
    }
}
