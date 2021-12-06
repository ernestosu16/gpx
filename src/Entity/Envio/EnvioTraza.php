<?php

namespace App\Entity\Envio;

use App\Entity\_Entity_;
use App\Entity\Estructura;
use App\Entity\Nomenclador;
use App\Entity\Trabajador;
use App\Repository\Envio\EnvioTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvioTrazaRepository::class)]
class EnvioTraza extends _Entity_
{

    #[ORM\Column(type: 'datetime', length: 10, nullable: false)]
    private \DateTime $fecha;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $peso;

    #[ORM\ManyToOne(targetEntity: Envio::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'envio_id', referencedColumnName: 'id', nullable: false)]
    private Envio $envio;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

    #[ORM\ManyToOne(targetEntity: Trabajador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'trabajador_id', referencedColumnName: 'id', nullable: true)]
    private ?Trabajador $trabajador;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'estructura_origen_id', referencedColumnName: 'id', nullable: false)]
    private Estructura $estructura_origen;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'estructura_destino_id', referencedColumnName: 'id', nullable: true)]
    private ?Estructura $estructura_destino;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'canal_id', referencedColumnName: 'id', nullable: false)]
    private ?Nomenclador $canal;

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
     */
    public function setFecha(\DateTime $fecha): void
    {
        $this->fecha = $fecha;
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
     */
    public function setPeso(float $peso): void
    {
        $this->peso = $peso;
    }

    /**
     * @return Envio
     */
    public function getEnvio(): Envio
    {
        return $this->envio;
    }

    /**
     * @param Envio $envio
     */
    public function setEnvio(Envio $envio): void
    {
        $this->envio = $envio;
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
     */
    public function setEstado(Nomenclador $estado): void
    {
        $this->estado = $estado;
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
     */
    public function setTrabajador(?Trabajador $trabajador): void
    {
        $this->trabajador = $trabajador;
    }

    /**
     * @return Estructura
     */
    public function getEstructuraOrigen(): Estructura
    {
        return $this->estructura_origen;
    }

    /**
     * @param Estructura $estructura_origen
     */
    public function setEstructuraOrigen(Estructura $estructura_origen): void
    {
        $this->estructura_origen = $estructura_origen;
    }

    /**
     * @return Estructura|null
     */
    public function getEstructuraDestino(): ?Estructura
    {
        return $this->estructura_destino;
    }

    /**
     * @param Estructura|null $estructura_destino
     */
    public function setEstructuraDestino(?Estructura $estructura_destino): void
    {
        $this->estructura_destino = $estructura_destino;
    }

    /**
     * @return Nomenclador|null
     */
    public function getCanal(): ?Nomenclador
    {
        return $this->canal;
    }

    /**
     * @param Nomenclador|null $canal
     */
    public function setCanal(?Nomenclador $canal): void
    {
        $this->canal = $canal;
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
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

}
