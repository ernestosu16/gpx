<?php


namespace App\Utils;


use App\Entity\Localizacion;
use App\Entity\Nomenclador;
use App\Entity\Pais;
use App\Entity\Persona;

class EnvioPreRecepcion
{
    private string $noGuia;
    private string $codTracking;
    private float $peso;
    //nacionalidad_remitente
    private string $paisOrigen;
    //currier y/o tipo de producto
    private string $agencia;
    //interes_aduana
    private bool $entidadCtrlAduana;
    private string $provincia;
    private string $municipio;
    private string $pareo;
    //Array de nomencladores de tipo anomalia
    private array $irregularidades;

    private Persona $destinatario;
    private Persona $remitente;
    private array $direcciones;

    /**
     * EnvioPreRecepcion constructor.
     * @param string $noGuia
     */
    public function __construct()
    {
        $this->irregularidades = [];
        $this->direcciones = [];
    }

    /**
     * @return string
     */
    public function getNoGuia(): string
    {
        return $this->noGuia;
    }

    /**
     * @param string $noGuia
     */
    public function setNoGuia(string $noGuia): void
    {
        $this->noGuia = $noGuia;
    }

    /**
     * @return string
     */
    public function getCodTracking(): string
    {
        return $this->codTracking;
    }

    /**
     * @param string $codTracking
     */
    public function setCodTracking(string $codTracking): void
    {
        $this->codTracking = $codTracking;
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
     * @return string
     */
    public function getPaisOrigen(): string
    {
        return $this->paisOrigen;
    }

    /**
     * @param string $paisOrigen
     */
    public function setPaisOrigen(string $paisOrigen): void
    {
        $this->paisOrigen = $paisOrigen;
    }

    /**
     * @return string
     */
    public function getAgencia(): string
    {
        return $this->agencia;
    }

    /**
     * @param string $agencia
     */
    public function setAgencia(string $agencia): void
    {
        $this->agencia = $agencia;
    }

    /**
     * @return bool
     */
    public function isEntidadCtrlAduana(): bool
    {
        return $this->entidadCtrlAduana;
    }

    /**
     * @param bool $entidadCtrlAduana
     */
    public function setEntidadCtrlAduana(bool $entidadCtrlAduana): void
    {
        $this->entidadCtrlAduana = $entidadCtrlAduana;
    }

    /**
     * @return string
     */
    public function getProvincia(): string
    {
        return $this->provincia;
    }

    /**
     * @param string $provincia
     */
    public function setProvincia(string $provincia): void
    {
        $this->provincia = $provincia;
    }

    /**
     * @return string
     */
    public function getMunicipio(): string
    {
        return $this->municipio;
    }

    /**
     * @param string $municipio
     */
    public function setMunicipio(string $municipio): void
    {
        $this->municipio = $municipio;
    }

    /**
     * @return string
     */
    public function getPareo(): string
    {
        return $this->pareo;
    }

    /**
     * @param string $pareo
     */
    public function setPareo(string $pareo): void
    {
        $this->pareo = $pareo;
    }

    /**
     * @return array
     */
    public function getIrregularidades(): array
    {
        return $this->irregularidades;
    }

    /**
     * @param array $irregularidades
     */
    public function setIrregularidades(array $irregularidades): void
    {
        $this->irregularidades = $irregularidades;
    }

    /**
     * @return Persona
     */
    public function getDestinatario(): Persona
    {
        return $this->destinatario;
    }

    /**
     * @param Persona $destinatario
     */
    public function setDestinatario(Persona $destinatario): void
    {
        $this->destinatario = $destinatario;
    }

    /**
     * @return Persona
     */
    public function getRemitente(): Persona
    {
        return $this->remitente;
    }

    /**
     * @param Persona $remitente
     */
    public function setRemitente(Persona $remitente): void
    {
        $this->remitente = $remitente;
    }

    /**
     * @return array
     */
    public function getDirecciones(): array
    {
        return $this->direcciones;
    }

    /**
     * @param array $direcciones
     */
    public function setDirecciones(array $direcciones): void
    {
        $this->direcciones = $direcciones;
    }


}