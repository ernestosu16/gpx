<?php

namespace App\Entity;

use App\Repository\FacturaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:FacturaTrazaRepository::class)]
class FacturaTraza extends _Entity_
{
    #[ORM\Column(type: 'datetime')]
    private $fecha;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $estado;

    #[ORM\ManyToOne(targetEntity: Factura::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $factura;

    #[ORM\Column(type: 'string', length: 15)]
    private $ip;

    #[ORM\ManyToOne(targetEntity: Trabajador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $trabajador;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $estructura;


    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     * @return FacturaTraza
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     * @return FacturaTraza
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * @param mixed $factura
     * @return FacturaTraza
     */
    public function setFactura($factura)
    {
        $this->factura = $factura;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     * @return FacturaTraza
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTrabajador()
    {
        return $this->trabajador;
    }

    /**
     * @param mixed $trabajador
     * @return FacturaTraza
     */
    public function setTrabajador($trabajador)
    {
        $this->trabajador = $trabajador;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstructura()
    {
        return $this->estructura;
    }

    /**
     * @param mixed $estructura
     * @return FacturaTraza
     */
    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;
        return $this;
    }


}
