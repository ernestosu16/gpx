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

    #[ORM\Column(type: 'json')]
    private array $datos_despacho = array();

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private string $provincia_aduana;

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    private string $municipio_aduana;

    #[ORM\Column(type: 'boolean')]
    private bool $arancel = false;

    #[ORM\Column(type: 'boolean')]
    private bool $procesado = false;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

    /*
    #[ORM\ManyToOne(targetEntity: Fichero::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'fichero_id', referencedColumnName: 'id', nullable: false)]
    private Fichero $fichero;
    */

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
     * @return array
     */
    public function getDatosDespacho(): array
    {
        return $this->datos_despacho;
    }

    /**
     * @param array $datos_despacho
     */
    public function setDatosDespacho(array $datos_despacho): void
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
     * @return bool
     */
    public function isArancel(): bool
    {
        return $this->arancel;
    }

    /**
     * @param bool $arancel
     */
    public function setArancel(bool $arancel): void
    {
        $this->arancel = $arancel;
    }

    /**
     * @return bool
     */
    public function isProcesado(): bool
    {
        return $this->procesado;
    }

    /**
     * @param bool $procesado
     */
    public function setProcesado(bool $procesado): void
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
