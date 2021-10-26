<?php


namespace App\Utils;


use App\Entity\Persona;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class EnvioPreRecepcion
{
    #[SerializedName('id')]
    public ?string $id;

    #[SerializedName('no_guia')]
    public ?string $no_guia;

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
    public ?string $pareo = "";

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
    public ?Persona $destinatario;

    #[SerializedName('remitente')]
    public ?Persona $remitente;

    #[SerializedName('modo_recepcion')]
    public string $modo_recepcion = "";

   public function __construct()
    {
    }


}