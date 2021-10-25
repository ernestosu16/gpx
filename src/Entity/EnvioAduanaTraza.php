<?php

namespace App\Entity;

use App\Repository\EnvioAduanaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvioAduanaTraza::class)]
class EnvioAduanaTraza extends _Entity_
{

    #[ORM\Column(type: 'datetime', length: 10, nullable: false)]
    private \DateTime $fecha;

    #[ORM\ManyToOne(targetEntity: Envio::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'envio_id', referencedColumnName: 'id', nullable: false)]
    private Envio $envio;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

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

}
