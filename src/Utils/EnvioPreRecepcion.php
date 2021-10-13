<?php


namespace App\Utils;


use App\Entity\Localizacion;
use App\Entity\Nomenclador;
use App\Entity\Pais;
use App\Entity\Persona;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use phpDocumentor\Reflection\Location;

class EnvioPreRecepcion
{
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
     * @var Nomenclador[]
     * @Type("array<App\Entity\Nomenclador>")
     * @SerializedName(name="irregularidades")
     */
    public array $irregularidades;

    #[SerializedName('destinatario')]
    public Persona $destinatario;

    #[SerializedName('remitente')]
    public Persona $remitente;

    /**
     * @var EnvioDireccion[]
     * @Type("array<App\Utils\EnvioDireccion>")
     * @SerializedName(name="direcciones")
     */
    public array $direcciones;

    /**
     * @param string $no_guia
     */
    public function __construct()
    {
    }


}