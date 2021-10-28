<?php

namespace App\Entity;

use App\Repository\SacaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SacaTrazaRepository::class)]
class SacaTraza extends _Entity_
{
    #[ORM\Column(type: 'datetime', length: 10, nullable: false)]
    private \DateTime $fecha;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $peso;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

    #[ORM\ManyToOne(targetEntity: Saca::class)]
    #[ORM\JoinColumn(name: 'saca_id', referencedColumnName: 'id', nullable: false)]
    private Saca $saca;

    #[ORM\ManyToOne(targetEntity: Factura::class)]
    #[ORM\JoinColumn(name: 'factura_id', referencedColumnName: 'id', nullable: true)]
    private ?Factura $factura;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'estructura_id', referencedColumnName: 'id', nullable: true)]
    private ?Estructura $estructura;

    #[ORM\ManyToOne(targetEntity: Trabajador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'trabajador_id', referencedColumnName: 'id', nullable: true)]
    private ?Trabajador $trabajador;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private string $ip;

    public function __construct()
    {
    }

    /**
     * @return \DateTime
     */
    public function getFecha(): \DateTime
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     * @return SacaTraza
     */
    public function setFecha(\DateTime $fecha): SacaTraza
    {
        $this->fecha = $fecha;
        return $this;
    }

    /**
     * @return float
     */
    public function getPeso(): float
    {
        return $this->peso;
    }

    /**
     * @param float $peso
     * @return SacaTraza
     */
    public function setPeso(float $peso): SacaTraza
    {
        $this->peso = $peso;
        return $this;
    }

    /**
     * @return Nomenclador
     */
    public function getEstado(): Nomenclador
    {
        return $this->estado;
    }

    /**
     * @param Nomenclador $estado
     * @return SacaTraza
     */
    public function setEstado(Nomenclador $estado): SacaTraza
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * @return Saca
     */
    public function getSaca(): Saca
    {
        return $this->saca;
    }

    /**
     * @param Saca $saca
     * @return SacaTraza
     */
    public function setSaca(Saca $saca): SacaTraza
    {
        $this->saca = $saca;
        return $this;
    }

    /**
     * @return Factura|null
     */
    public function getFactura(): ?Factura
    {
        return $this->factura;
    }

    /**
     * @param Factura|null $factura
     * @return SacaTraza
     */
    public function setFactura(?Factura $factura): SacaTraza
    {
        $this->factura = $factura;
        return $this;
    }

    /**
     * @return Estructura|null
     */
    public function getEstructura(): ?Estructura
    {
        return $this->estructura;
    }

    /**
     * @param Estructura|null $estructura
     * @return SacaTraza
     */
    public function setEstructura(?Estructura $estructura): SacaTraza
    {
        $this->estructura = $estructura;
        return $this;
    }

    /**
     * @return Trabajador|null
     */
    public function getTrabajador(): ?Trabajador
    {
        return $this->trabajador;
    }

    /**
     * @param Trabajador|null $trabajador
     * @return SacaTraza
     */
    public function setTrabajador(?Trabajador $trabajador): SacaTraza
    {
        $this->trabajador = $trabajador;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return SacaTraza
     */
    public function setIp(string $ip): SacaTraza
    {
        $this->ip = $ip;
        return $this;
    }


}
