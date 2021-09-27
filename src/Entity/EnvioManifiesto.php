<?php

namespace App\Entity;

use App\Repository\EnvioManifiestoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnvioManifiestoRepository::class)]
class EnvioManifiesto extends _Entity_
{
    #[ORM\Column(type: 'string', length: 15)]
    private string $codigo;

    #[ORM\Column(type: 'float')]
    private float $peso;

    private \DateTime $fecha;

    #[ORM\Column(type: 'string', length: 20)]
    private string $agencia_origen;

    #[ORM\Column(type: 'string', length: 20)]
    private string $no_guia_aerea;

    #[ORM\Column(type: 'string', length: 20)]
    private string $no_vuelo;

    #[ORM\Column(type: 'string', length: 30)]
    private string $pais_origen;

    #[ORM\Column(type: 'string', length: 50)]
    private string $descripcion;

    #[ORM\ManyToOne(targetEntity: Persona::class)]
    #[ORM\JoinColumn(name: 'destinatario_id', referencedColumnName: 'id', nullable: false)]
    private Persona $destinatario;

    #[ORM\Column(type: 'string', length: 5)]
    private string $nacionalidad_destinatario;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private \DateTime $fecha_nacimiento_destinatario;

    #[ORM\Column(type: 'string', length: 15)]
    private string $telefono_destinatario;

    #[ORM\Column(type: 'string', length: 25)]
    private string $calle_destinatario;

    #[ORM\Column(type: 'string', length: 25)]
    private string $entre_calle_destinatario;

    #[ORM\Column(type: 'string', length: 25)]
    private string $y_calle_destinatario;

    #[ORM\Column(type: 'string', length: 10)]
    private string $no_destinatario;

    #[ORM\Column(type: 'string', length: 10)]
    private string $piso_destinatario;

    #[ORM\Column(type: 'string', length: 10)]
    private string $apto_destinatario;

    #[ORM\Column(type: 'string', length: 10)]
    private string $provincia_destinatario;

    #[ORM\Column(type: 'string', length: 10)]
    private string $municipio_destinatario;

    #[ORM\ManyToOne(targetEntity: Persona::class)]
    #[ORM\JoinColumn(name: 'remitente_id', referencedColumnName: 'id', nullable: false)]
    private Persona $remitente;

    #[ORM\Column(type: 'string', length: 11)]
    private string $nacionalidad_remitente;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private \DateTime $fecha_nacimiento_remitente;

    #[ORM\Column(type: 'boolean')]
    private bool $recepcionado;

    #[ORM\Column(type: 'boolean')]
    private bool $interes_aduana;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false)]
    private Estructura $empresa;

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
     * @return \DateTime
     */
    public function getFechaNacimientoDestinatario(): \DateTime
    {
        return $this->fecha_nacimiento_destinatario;
    }

    /**
     * @param \DateTime $fecha_nacimiento_destinatario
     */
    public function setFechaNacimientoDestinatario(\DateTime $fecha_nacimiento_destinatario): void
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
     * @return \DateTime
     */
    public function getFechaNacimientoRemitente(): \DateTime
    {
        return $this->fecha_nacimiento_remitente;
    }

    /**
     * @param \DateTime $fecha_nacimiento_remitente
     */
    public function setFechaNacimientoRemitente(\DateTime $fecha_nacimiento_remitente): void
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


}
