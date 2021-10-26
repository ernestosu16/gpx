<?php

namespace App\Entity;

use App\Repository\SacaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:SacaTrazaRepository::class)]
class SacaTraza extends _Entity_
{
    #[ORM\Column(type: 'datetime')]
    private $fecha;

    #[ORM\Column(type: 'float', nullable: false)]
    private $peso;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $estado;

    #[ORM\ManyToOne(targetEntity: Saca::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $saca;

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
     * @return SacaTraza
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param mixed $peso
     * @return SacaTraza
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
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
     * @return SacaTraza
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSaca()
    {
        return $this->saca;
    }

    /**
     * @param mixed $saca
     * @return SacaTraza
     */
    public function setSaca($saca)
    {
        $this->saca = $saca;
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
     * @return SacaTraza
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
     * @return SacaTraza
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
     * @return SacaTraza
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
     * @return SacaTraza
     */
    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;
        return $this;
    }


}
