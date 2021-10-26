<?php

namespace App\DTO;

use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\SerializedName;

class Insertar
{
    #[SerializedName('numeroOrigen')]
    private string $cod_tracking;

    #[SerializedName('peso')]
    private float $peso;

    #[SerializedName('agenciaOrigen')]
    private string $agencia_origen;

    #[SerializedName('paisOrigen')]
    private string $pais_origen;

    #[SerializedName('codigoProvincia')]
    private string $codigo_provincia;

    #[SerializedName('codigoMunicipio')]
    private string $codigo_municipio;

    #[SerializedName('estadoEmbalaje')]
    private string $estado_embalaje;

    #[SerializedName('fecha')]
    private string $fecha;

    /**
     * EnvioAduanaDom constructor.
     * @param string $cod_tracking
     * @param string $agencia_origen
     * @param string $codigo_provincia
     * @param string $codigo_municipio
     * @param string $pais_origen
     * @param float $peso
     */
    public function __construct(string $cod_tracking, string $agencia_origen, string $codigo_provincia, string $codigo_municipio, string $pais_origen, float $peso)
    {
        $this->cod_tracking = $cod_tracking;
        $this->peso = $peso;
        $this->agencia_origen = $agencia_origen;
        $this->pais_origen = $pais_origen;
        $this->codigo_provincia = $codigo_provincia;
        $this->codigo_municipio = $codigo_municipio;
        $this->estado_embalaje = 'S';
        $this->fecha = date_format(new \DateTime('now'), 'Y-m-d H:i:s' );
    }

    /**
     * @return string
     */
    public function getFecha(): string
    {
        return $this->fecha;
    }

    /**
     * @param string $fecha
     */
    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
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
    public function getAgenciaOrigen(): string
    {
        return $this->agencia_origen;
    }

    /**
     * @param string $agencia_origen
     */
    public function setAgenciaOrigen(string $agencia_origen): void
    {
        $this->agencia_origen = $agencia_origen;
    }

    /**
     * @return string
     */
    public function getCodigoProvincia(): string
    {
        return $this->codigo_provincia;
    }

    /**
     * @param string $codigo_provincia
     */
    public function setCodigoProvincia(string $codigo_provincia): void
    {
        $this->codigo_provincia = $codigo_provincia;
    }

    /**
     * @return string
     */
    public function getCodigoMunicipio(): string
    {
        return $this->codigo_municipio;
    }

    /**
     * @param string $codigo_municipio
     */
    public function setCodigoMunicipio(string $codigo_municipio): void
    {
        $this->codigo_municipio = $codigo_municipio;
    }

    /**
     * @return string
     */
    public function getEstadoEmbalaje(): string
    {
        return $this->estado_embalaje;
    }

    /**
     * @param string $estado_embalaje
     */
    public function setEstadoEmbalaje(string $estado_embalaje): void
    {
        $this->estado_embalaje = $estado_embalaje;
    }

    /**
     * @return string
     */
    public function getPaisOrigen(): string
    {
        return $this->pais_origen;
    }

    /**
     * @param string $pais_origen
     */
    public function setPaisOrigen(string $pais_origen): void
    {
        $this->pais_origen = $pais_origen;
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
}
