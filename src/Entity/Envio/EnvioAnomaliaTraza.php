<?php

namespace App\Entity\Envio;

use App\Entity\_Entity_;
use App\Entity\Nomenclador;
use App\Repository\Envio\EnvioAnomaliaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvioAnomaliaTrazaRepository::class)]
class EnvioAnomaliaTraza extends _Entity_
{
    #[ORM\Column(type: 'string', length: 2550, nullable: false)]
    private string $descripcion;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'anomalia_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $anomalia;

    #[ORM\ManyToOne(targetEntity: EnvioTraza::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'envio_traza_id', referencedColumnName: 'id', nullable: false)]
    private EnvioTraza $envio_traza;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return Nomenclador
     */
    public function getAnomalia(): Nomenclador
    {
        return $this->anomalia;
    }

    /**
     * @param Nomenclador $anomalia
     */
    public function setAnomalia(Nomenclador $anomalia): void
    {
        $this->anomalia = $anomalia;
    }

    /**
     * @return EnvioTraza
     */
    public function getEnvioTraza(): EnvioTraza
    {
        return $this->envio_traza;
    }

    /**
     * @param EnvioTraza $envio_traza
     */
    public function setEnvioTraza(EnvioTraza $envio_traza): void
    {
        $this->envio_traza = $envio_traza;
    }

}
