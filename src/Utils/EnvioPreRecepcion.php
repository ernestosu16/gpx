<?php


namespace App\Utils;


use App\Entity\Persona;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class EnvioPreRecepcion
{
    #[SerializedName('id')]
    public string $id;

    #[SerializedName('no_guia')]
    public string $no_guia;

    #[SerializedName('cod_tracking')]
    public string $cod_tracking;

    #[SerializedName('peso')]
    public float $peso;

    //nacionalidad_remitente
    #[SerializedName('pais_origen')]
    public string $pais_origen;

    //currier y/o tipo de producto
    #[SerializedName('agencia')]
    public string $agencia;

    //interes_aduana
    #[SerializedName('entidad_ctrl_aduana')]
    public bool $entidad_ctrl_aduana;

    #[SerializedName('provincia')]
    public ?string $provincia;

    #[SerializedName('municipio')]
    public ?string $municipio;

    #[SerializedName('pareo')]
    public string $pareo = "";

    #[SerializedName('requiere_pareo')]
    public bool $requiere_pareo = false;

    //Array de nomencladores de tipo anomalia
    /**
     * @var EnvioAnomalia[]
     * @Type("array<App\Utils\EnvioAnomalia>")
     * @SerializedName(name="irregularidades")
     */
    public array $irregularidades = [];

    #[SerializedName('destinatario')]
    public Persona $destinatario;

    #[SerializedName('remitente')]
    public Persona $remitente;




    public function __construct()
    {
    }

    /**
    /**
     * @param string $no_guia
     * @return EnvioPreRecepcion
     */
    public function setNoGuia(string $no_guia): EnvioPreRecepcion
    {
        $this->no_guia = $no_guia;
        return $this;
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
     * @return EnvioPreRecepcion
     */
    public function setCodTracking(string $cod_tracking): EnvioPreRecepcion
    {
        $this->cod_tracking = $cod_tracking;
        return $this;
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
     * @return EnvioPreRecepcion
     */
    public function setPeso(float $peso): EnvioPreRecepcion
    {
        $this->peso = $peso;
        return $this;
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
     * @return EnvioPreRecepcion
     */
    public function setPaisOrigen(string $pais_origen): EnvioPreRecepcion
    {
        $this->pais_origen = $pais_origen;
        return $this;
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
     * @return EnvioPreRecepcion
     */
    public function setAgencia(string $agencia): EnvioPreRecepcion
    {
        $this->agencia = $agencia;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEntidadCtrlAduana(): bool
    {
        return $this->entidad_ctrl_aduana;
    }

    /**
     * @param bool $entidad_ctrl_aduana
     * @return EnvioPreRecepcion
     */
    public function setEntidadCtrlAduana(bool $entidad_ctrl_aduana): EnvioPreRecepcion
    {
        $this->entidad_ctrl_aduana = $entidad_ctrl_aduana;
        return $this;
    }

    /**
     * @return string|null
     */
    public function __construct()
    {
        return $this->provincia;
    }

    /**
     * @param string|null $provincia
     * @return EnvioPreRecepcion
     */
    public function setProvincia(?string $provincia): EnvioPreRecepcion
    {
        $this->provincia = $provincia;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    /**
     * @param string|null $municipio
     * @return EnvioPreRecepcion
     */
    public function setMunicipio(?string $municipio): EnvioPreRecepcion
    {
        $this->municipio = $municipio;
        return $this;
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
     * @return EnvioPreRecepcion
     */
    public function setPareo(string $pareo): EnvioPreRecepcion
    {
        $this->pareo = $pareo;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequierePareo(): bool
    {
        return $this->requiere_pareo;
    }

    /**
     * @param bool $requiere_pareo
     * @return EnvioPreRecepcion
     */
    public function setRequierePareo(bool $requiere_pareo): EnvioPreRecepcion
    {
        $this->requiere_pareo = $requiere_pareo;
        return $this;
    }

    /**
     * @return EnvioAnomalia[]
     */
    public function getIrregularidades(): array
    {
        return $this->irregularidades;
    }

    /**
     * @param EnvioAnomalia[] $irregularidades
     * @return EnvioPreRecepcion
     */
    public function setIrregularidades(array $irregularidades): EnvioPreRecepcion
    {
        $this->irregularidades = $irregularidades;
        return $this;
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
     * @return EnvioPreRecepcion
     */
    public function setDestinatario(Persona $destinatario): EnvioPreRecepcion
    {
        $this->destinatario = $destinatario;
        return $this;
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
     * @return EnvioPreRecepcion
     */
    public function setRemitente(Persona $remitente): EnvioPreRecepcion
    {
        $this->remitente = $remitente;
        return $this;
    }

    /**
     * @return EnvioDireccion[]
     */
    public function getDirecciones(): array
    {
        return $this->direcciones;
    }

    /**
     * @param EnvioDireccion[] $direcciones
     * @return EnvioPreRecepcion
     */
    public function setDirecciones(array $direcciones): EnvioPreRecepcion
    {
        $this->direcciones = $direcciones;
        return $this;
    }


}