<?php

namespace App\Entity;

use App\Repository\EnvioAduanaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvioAduanaRepository::class)]
class EnvioAduana extends _Entity_
{
    #[ORM\OneToOne(targetEntity: Envio::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'envio_id', referencedColumnName: 'id', nullable: false)]
    private Envio $envio;

    #[ORM\Column(type: 'string', length: 13, nullable: true)]
    private string $cod_tracking;

    #[ORM\Column(type: 'string', length: 800, nullable: true)]
    private string $datos_despacho;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private string $provincia_aduana;

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    private string $municipio_aduana;

    #[ORM\Column(type: 'boolean')]
    private string $arancel;

    #[ORM\Column(type: 'boolean')]
    private string $procesado;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

    public function __construct()
    {
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
     * @return string
     */
    public function getCodTracking(): string
    {
        return $this->cod_tracking;
    }

    /**
     * @param string $cod_tracking
     */
    public function setCodTracking(string $cod_tracking): void
    {
        $this->cod_tracking = $cod_tracking;
    }

    /**
     * @return string
     */
    public function getDatosDespacho(): string
    {
        return $this->datos_despacho;
    }

    /**
     * @param string $datos_despacho
     */
    public function setDatosDespacho(string $datos_despacho): void
    {
        $this->datos_despacho = $datos_despacho;
    }

    /**
     * @return string
     */
    public function getProvinciaAduana(): string
    {
        return $this->provincia_aduana;
    }

    /**
     * @param string $provincia_aduana
     */
    public function setProvinciaAduana(string $provincia_aduana): void
    {
        $this->provincia_aduana = $provincia_aduana;
    }

    /**
     * @return string
     */
    public function getMunicipioAduana(): string
    {
        return $this->municipio_aduana;
    }

    /**
     * @param string $municipio_aduana
     */
    public function setMunicipioAduana(string $municipio_aduana): void
    {
        $this->municipio_aduana = $municipio_aduana;
    }

    /**
     * @return string
     */
    public function getArancel(): string
    {
        return $this->arancel;
    }

    /**
     * @param string $arancel
     */
    public function setArancel(string $arancel): void
    {
        $this->arancel = $arancel;
    }

    /**
     * @return string
     */
    public function getProcesado(): string
    {
        return $this->procesado;
    }

    /**
     * @param string $procesado
     */
    public function setProcesado(string $procesado): void
    {
        $this->procesado = $procesado;
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
