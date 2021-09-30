<?php

namespace App\Entity;

use App\Repository\EnvioManifiestoRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnvioManifiestoRepository::class)]
class EnvioManifiesto extends _Entity_
{
    #[ORM\Column(type: 'string', length: 15, nullable: false)]
    #[SerializedName('noEnvio')]
    private string $codigo;

    #[ORM\Column(type: 'float', nullable: false)]
    #[SerializedName('peso')]
    private float $peso;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    #[SerializedName('fechaImposicion')]
    private string $fecha;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private string $agencia_origen;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private string $no_guia_aerea;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private string $no_vuelo;

    #[ORM\Column(type: 'string', length: 30, nullable: false)]
    #[SerializedName('paisOrigen-Destino')]
    private string $pais_origen;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    #[SerializedName('descripcion')]
    private string $descripcion;

    #[ORM\ManyToOne(targetEntity: Persona::class)]
    #[ORM\JoinColumn(name: 'destinatario_id', referencedColumnName: 'id')]
    private Persona $destinatario;

    #[ORM\Column(type: 'string', length: 5, nullable: false)]
    #[SerializedName('nacionalidad')]
    private string $nacionalidad_destinatario;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    //#[Assert\NotBlank]
    private string $fecha_nacimiento_destinatario;

    #[ORM\Column(type: 'string', length: 15, nullable: false)]
    private string $telefono_destinatario;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $calle_destinatario;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $entre_calle_destinatario;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $y_calle_destinatario;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $no_destinatario;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $piso_destinatario;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $apto_destinatario;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $provincia_destinatario;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    private string $municipio_destinatario;

    #[ORM\ManyToOne(targetEntity: Persona::class)]
    #[ORM\JoinColumn(name: 'remitente_id', referencedColumnName: 'id', nullable: false)]
    private Persona $remitente;

    #[ORM\Column(type: 'string', length: 11, nullable: false)]
    private string $nacionalidad_remitente;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    //#[Assert\NotBlank]
    private string $fecha_nacimiento_remitente;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $recepcionado;

    #[ORM\Column(type: 'boolean')]
    private bool $interes_aduana;

    #[ORM\Column(type: 'boolean')]
    private bool $arancel;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false)]
    private Estructura $empresa;

    /**
     * EnvioManifiesto constructor.
     * @param string $codigo
     * @param float $peso
     * @param string $fecha
     * @param string $agencia_origen
     * @param string $no_guia_aerea
     * @param string $no_vuelo
     * @param string $pais_origen
     * @param string $descripcion
     * @param string $nacionalidad_destinatario
     * @param string $fecha_nacimiento_destinatario
     * @param string $telefono_destinatario
     * @param string $calle_destinatario
     * @param string $entre_calle_destinatario
     * @param string $y_calle_destinatario
     * @param string $no_destinatario
     * @param string $piso_destinatario
     * @param string $apto_destinatario
     * @param string $provincia_destinatario
     * @param string $municipio_destinatario
     * @param string $nacionalidad_remitente
     * @param string $fecha_nacimiento_remitente
     */
    public function __construct(string $codigo, float $peso, string $fecha, string $agencia_origen, string $no_guia_aerea, string $no_vuelo, string $pais_origen, string $descripcion, string $nacionalidad_destinatario, string $fecha_nacimiento_destinatario, string $telefono_destinatario, string $calle_destinatario, string $entre_calle_destinatario, string $y_calle_destinatario, string $no_destinatario, string $piso_destinatario, string $apto_destinatario, string $provincia_destinatario, string $municipio_destinatario, string $nacionalidad_remitente, string $fecha_nacimiento_remitente)
    {
        $this->codigo = $codigo;
        $this->peso = $peso;
        $this->fecha = $fecha;
        $this->agencia_origen = $agencia_origen;
        $this->no_guia_aerea = $no_guia_aerea;
        $this->no_vuelo = $no_vuelo;
        $this->pais_origen = $pais_origen;
        $this->descripcion = $descripcion;
        $this->nacionalidad_destinatario = $nacionalidad_destinatario;
        $this->fecha_nacimiento_destinatario = $fecha_nacimiento_destinatario;
        $this->telefono_destinatario = $telefono_destinatario;
        $this->calle_destinatario = $calle_destinatario;
        $this->entre_calle_destinatario = $entre_calle_destinatario;
        $this->y_calle_destinatario = $y_calle_destinatario;
        $this->no_destinatario = $no_destinatario;
        $this->piso_destinatario = $piso_destinatario;
        $this->apto_destinatario = $apto_destinatario;
        $this->provincia_destinatario = $provincia_destinatario;
        $this->municipio_destinatario = $municipio_destinatario;
        $this->nacionalidad_remitente = $nacionalidad_remitente;
        $this->fecha_nacimiento_remitente = $fecha_nacimiento_remitente;
        $this->recepcionado = false;
        $this->interes_aduana = false;
    }


    /**
     * @return string
     */
    public function getCodigo(): string
    {
        return $this->codigo;
    }

    /**
     * @param string $codigo
     */
    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
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
     * @return \DateTime
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
    public function getNoGuiaAerea(): string
    {
        return $this->no_guia_aerea;
    }

    /**
     * @param string $no_guia_aerea
     */
    public function setNoGuiaAerea(string $no_guia_aerea): void
    {
        $this->no_guia_aerea = $no_guia_aerea;
    }

    /**
     * @return string
     */
    public function getNoVuelo(): string
    {
        return $this->no_vuelo;
    }

    /**
     * @param string $no_vuelo
     */
    public function setNoVuelo(string $no_vuelo): void
    {
        $this->no_vuelo = $no_vuelo;
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
     * @return string
     */
    public function getNacionalidadDestinatario(): string
    {
        return $this->nacionalidad_destinatario;
    }

    /**
     * @param string $nacionalidad_destinatario
     */
    public function setNacionalidadDestinatario(string $nacionalidad_destinatario): void
    {
        $this->nacionalidad_destinatario = $nacionalidad_destinatario;
    }

    /**
     * @return string
     */
    public function getFechaNacimientoDestinatario(): string
    {
        return $this->fecha_nacimiento_destinatario;
    }

    /**
     * @param string $fecha_nacimiento_destinatario
     */
    public function setFechaNacimientoDestinatario(string $fecha_nacimiento_destinatario): void
    {
        $this->fecha_nacimiento_destinatario = $fecha_nacimiento_destinatario;
    }

    /**
     * @return string
     */
    public function getTelefonoDestinatario(): string
    {
        return $this->telefono_destinatario;
    }

    /**
     * @param string $telefono_destinatario
     */
    public function setTelefonoDestinatario(string $telefono_destinatario): void
    {
        $this->telefono_destinatario = $telefono_destinatario;
    }

    /**
     * @return string
     */
    public function getCalleDestinatario(): string
    {
        return $this->calle_destinatario;
    }

    /**
     * @param string $calle_destinatario
     */
    public function setCalleDestinatario(string $calle_destinatario): void
    {
        $this->calle_destinatario = $calle_destinatario;
    }

    /**
     * @return string
     */
    public function getEntreCalleDestinatario(): string
    {
        return $this->entre_calle_destinatario;
    }

    /**
     * @param string $entre_calle_destinatario
     */
    public function setEntreCalleDestinatario(string $entre_calle_destinatario): void
    {
        $this->entre_calle_destinatario = $entre_calle_destinatario;
    }

    /**
     * @return string
     */
    public function getYCalleDestinatario(): string
    {
        return $this->y_calle_destinatario;
    }

    /**
     * @param string $y_calle_destinatario
     */
    public function setYCalleDestinatario(string $y_calle_destinatario): void
    {
        $this->y_calle_destinatario = $y_calle_destinatario;
    }

    /**
     * @return string
     */
    public function getNoDestinatario(): string
    {
        return $this->no_destinatario;
    }

    /**
     * @param string $no_destinatario
     */
    public function setNoDestinatario(string $no_destinatario): void
    {
        $this->no_destinatario = $no_destinatario;
    }

    /**
     * @return string
     */
    public function getPisoDestinatario(): string
    {
        return $this->piso_destinatario;
    }

    /**
     * @param string $piso_destinatario
     */
    public function setPisoDestinatario(string $piso_destinatario): void
    {
        $this->piso_destinatario = $piso_destinatario;
    }

    /**
     * @return string
     */
    public function getAptoDestinatario(): string
    {
        return $this->apto_destinatario;
    }

    /**
     * @param string $apto_destinatario
     */
    public function setAptoDestinatario(string $apto_destinatario): void
    {
        $this->apto_destinatario = $apto_destinatario;
    }

    /**
     * @return string
     */
    public function getProvinciaDestinatario(): string
    {
        return $this->provincia_destinatario;
    }

    /**
     * @param string $provincia_destinatario
     */
    public function setProvinciaDestinatario(string $provincia_destinatario): void
    {
        $this->provincia_destinatario = $provincia_destinatario;
    }

    /**
     * @return string
     */
    public function getMunicipioDestinatario(): string
    {
        return $this->municipio_destinatario;
    }

    /**
     * @param string $municipio_destinatario
     */
    public function setMunicipioDestinatario(string $municipio_destinatario): void
    {
        $this->municipio_destinatario = $municipio_destinatario;
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
     * @return string
     */
    public function getNacionalidadRemitente(): string
    {
        return $this->nacionalidad_remitente;
    }

    /**
     * @param string $nacionalidad_remitente
     */
    public function setNacionalidadRemitente(string $nacionalidad_remitente): void
    {
        $this->nacionalidad_remitente = $nacionalidad_remitente;
    }

    /**
     * @return string
     */
    public function getFechaNacimientoRemitente(): string
    {
        return $this->fecha_nacimiento_remitente;
    }

    /**
     * @param string $fecha_nacimiento_remitente
     */
    public function setFechaNacimientoRemitente(string $fecha_nacimiento_remitente): void
    {
        $this->fecha_nacimiento_remitente = $fecha_nacimiento_remitente;
    }

    /**
     * @return bool
     */
    public function isRecepcionado(): bool
    {
        return $this->recepcionado;
    }

    /**
     * @param bool $recepcionado
     */
    public function setRecepcionado(bool $recepcionado): void
    {
        $this->recepcionado = $recepcionado;
    }

    /**
     * @return bool
     */
    public function isInteresAduana(): bool
    {
        return $this->interes_aduana;
    }

    /**
     * @param bool $interes_aduana
     */
    public function setInteresAduana(bool $interes_aduana): void
    {
        $this->interes_aduana = $interes_aduana;
    }

    /**
     * @return Estructura
     */
    public function getEmpresa(): Estructura
    {
        return $this->empresa;
    }

    /**
     * @param Estructura $empresa
     */
    public function setEmpresa(Estructura $empresa): void
    {
        $this->empresa = $empresa;
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


}
