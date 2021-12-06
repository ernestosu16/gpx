<?php

namespace App\Entity\Envio;

use App\Entity\_Entity_;
use App\Entity\Estructura;
use App\Entity\Nomenclador;
use App\Entity\Trabajador;
use App\Repository\Envio\FacturaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturaTrazaRepository::class)]
class FacturaTraza extends _Entity_
{
    #[ORM\Column(type: 'datetime', length: 10, nullable: false)]
    private \DateTime $fecha;

    #[ORM\ManyToOne(targetEntity: Trabajador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'trabajador_id', referencedColumnName: 'id', nullable: true)]
    private ?Trabajador $trabajador;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'estructura_id', referencedColumnName: 'id', nullable: true)]
    private ?Estructura $estructura;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado_factura;

    #[ORM\ManyToOne(targetEntity: Factura::class)]
    #[ORM\JoinColumn(name: 'factura_id', referencedColumnName: 'id', nullable: true)]
    private ?Factura $factura;

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
     * @return FacturaTraza
     */
    public function setFecha(\DateTime $fecha): FacturaTraza
    {
        $this->fecha = $fecha;
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
     * @return FacturaTraza
     */
    public function setTrabajador(?Trabajador $trabajador): FacturaTraza
    {
        $this->trabajador = $trabajador;
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
     * @return FacturaTraza
     */
    public function setEstructura(?Estructura $estructura): FacturaTraza
    {
        $this->estructura = $estructura;
        return $this;
    }

    /**
     * @return Nomenclador
     */
    public function getEstadoFactura(): Nomenclador
    {
        return $this->estado_factura;
    }

    /**
     * @param Nomenclador $estado_factura
     * @return FacturaTraza
     */
    public function setEstadoFactura(Nomenclador $estado_factura): FacturaTraza
    {
        $this->estado_factura = $estado_factura;
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
     * @return FacturaTraza
     */
    public function setFactura(?Factura $factura): FacturaTraza
    {
        $this->factura = $factura;
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
     * @return FacturaTraza
     */
    public function setIp(string $ip): FacturaTraza
    {
        $this->ip = $ip;
        return $this;
    }


}
