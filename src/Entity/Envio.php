<?php

namespace App\Entity;

use App\Repository\EnvioRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: EnvioRepository::class)]
#[ORM\Index(columns: ['estructura_origen_id'], name: 'IDX_ESTRUCTURA_ORIGEN_ID')]
#[ORM\Index(columns: ['estructura_destino_id'], name: 'IDX_ESTRUCTURA_DESTINO_ID')]
#[ORM\Index(columns: ['destinatario_id'], name: 'IDX_DESTINATARIO_ID')]
#[ORM\Index(columns: ['remitente_id'], name: 'IDX_REMITENTE_ID')]
#[ORM\Index(columns: ['endosatario_id'], name: 'IDX_ENDOSATARIO_ID')]
class Envio extends _Entity_
{
    #[ORM\Column(type: 'datetime', length: 10, nullable: false)]
    private \DateTime $fecha_recepcion;

    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    private string $cod_tracking;

    #[ORM\Column(type: 'string', length: 13, nullable: true)]
    private ?string $pareo;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $peso;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Estructura $estructura_origen;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Estructura $estructura_destino;

    #[ORM\ManyToOne(targetEntity: Persona::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Persona $destinatario;

    #[ORM\ManyToOne(targetEntity: Persona::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Persona $remitente;

    // cambiar Nomenclador por endosatario_id cuando se cree
    #[ORM\ManyToOne(targetEntity: Persona::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Persona $endosatario;

    // cambiar Nomenclador por FormaEntrega cuando se cree
    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'forma_entrega_id', referencedColumnName: 'id', nullable: true)]
    private ?Nomenclador $forma_entrega;

    // cambiar Nomenclador por Canal cuando se cree
    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'canal_id', referencedColumnName: 'id', nullable: true)]
    private ?Nomenclador $canal;

    // cambiar Nomenclador por Agencia cuando se cree
    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'agencia_id', referencedColumnName: 'id', nullable: true)]
    private ?Nomenclador $agencia;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

    #[ORM\ManyToOne(targetEntity: Pais::class)]
    #[ORM\JoinColumn(name: 'pais_origen_id', referencedColumnName: 'id', nullable: false)]
    private Pais $pais_origen;

    #[ORM\ManyToOne(targetEntity: Pais::class)]
    #[ORM\JoinColumn(name: 'pais_destino_id', referencedColumnName: 'id', nullable: false)]
    private Pais $pais_destino;

    // cambiar Nomenclador por saca cuando se cree
    #[ORM\ManyToOne(targetEntity: Saca::class)]
    #[ORM\JoinColumn(name: 'saca_id', referencedColumnName: 'id', nullable: true)]
    private ?Saca $saca;

    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(name: 'empresa_id', referencedColumnName: 'id', nullable: false)]
    private Estructura $empresa;

    #[ORM\ManyToOne(targetEntity: Localizacion::class)]
    #[ORM\JoinColumn(name: 'provincia_id', referencedColumnName: 'id', nullable: false)]
    private Localizacion $provincia;

    #[ORM\ManyToOne(targetEntity: Localizacion::class)]
    #[ORM\JoinColumn(name: 'municipio_id', referencedColumnName: 'id', nullable: false)]
    private Localizacion $municipio;

    #[ORM\ManyToOne(targetEntity: Factura::class)]
    #[ORM\JoinColumn(name: 'factura_id', referencedColumnName: 'id', nullable: true)]
    private ?Factura $factura;

    #[ORM\Column(type: 'json')]
    private array $direcciones = array();


    #[Pure]
    public function __construct()
    {
    }

    public function __toString(): string
    {
        return $this->cod_tracking;
    }

    /**
     * @return \DateTime
     */
    public function getFechaRecepcion(): \DateTime
    {
        return $this->fecha_recepcion;
    }

    /**
     * @param \DateTime $fecha_recepcion
     */
    public function setFechaRecepcion(\DateTime $fecha_recepcion): void
    {
        $this->fecha_recepcion = $fecha_recepcion;
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
     * @return string|null
     */
    public function getPareo(): ?string
    {
        return $this->pareo;
    }

    /**
     * @param string|null $pareo
     */
    public function setPareo(?string $pareo): void
    {
        $this->pareo = $pareo;
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
     * @return Estructura|null
     */
    public function getEstructuraOrigen(): ?Estructura
    {
        return $this->estructura_origen;
    }

    /**
     * @param Estructura|null $estructura_origen
     */
    public function setEstructuraOrigen(?Estructura $estructura_origen): void
    {
        $this->estructura_origen = $estructura_origen;
    }

    /**
     * @return Estructura|null
     */
    public function getEstructuraDestino(): ?Estructura
    {
        return $this->estructura_destino;
    }

    /**
     * @param Estructura|null $estructura_destino
     */
    public function setEstructuraDestino(?Estructura $estructura_destino): void
    {
        $this->estructura_destino = $estructura_destino;
    }

    /**
     * @return Persona|null
     */
    public function getDestinatario(): ?Persona
    {
        return $this->destinatario;
    }

    /**
     * @param Persona|null $destinatario
     */
    public function setDestinatario(?Persona $destinatario): void
    {
        $this->destinatario = $destinatario;
    }

    /**
     * @return Persona|null
     */
    public function getRemitente(): ?Persona
    {
        return $this->remitente;
    }

    /**
     * @param Persona|null $remitente
     */
    public function setRemitente(?Persona $remitente): void
    {
        $this->remitente = $remitente;
    }

    /**
     * @return Persona|null
     */
    public function getEndosatario(): ?Persona
    {
        return $this->endosatario;
    }

    /**
     * @param Persona|null $endosatario
     */
    public function setEndosatario(?Persona $endosatario): void
    {
        $this->endosatario = $endosatario;
    }

    /**
     * @return Nomenclador|null
     */
    public function getFormaEntrega(): ?Nomenclador
    {
        return $this->forma_entrega;
    }

    /**
     * @param Nomenclador|null $forma_entrega
     */
    public function setFormaEntrega(?Nomenclador $forma_entrega): void
    {
        $this->forma_entrega = $forma_entrega;
    }

    /**
     * @return Nomenclador|null
     */
    public function getCanal(): ?Nomenclador
    {
        return $this->canal;
    }

    /**
     * @param Nomenclador|null $canal
     */
    public function setCanal(?Nomenclador $canal): void
    {
        $this->canal = $canal;
    }

    /**
     * @return Nomenclador|null
     */
    public function getAgencia(): ?Nomenclador
    {
        return $this->agencia;
    }

    /**
     * @param Nomenclador|null $agencia
     */
    public function setAgencia(?Nomenclador $agencia): void
    {
        $this->agencia = $agencia;
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

    /**
     * @return Pais
     */
    public function getPaisOrigen(): Pais
    {
        return $this->pais_origen;
    }

    /**
     * @param Pais $pais_origen
     */
    public function setPaisOrigen(Pais $pais_origen): void
    {
        $this->pais_origen = $pais_origen;
    }

    /**
     * @return Pais
     */
    public function getPaisDestino(): Pais
    {
        return $this->pais_destino;
    }

    /**
     * @param Pais $pais_destino
     */
    public function setPaisDestino(Pais $pais_destino): void
    {
        $this->pais_destino = $pais_destino;
    }

    /**
     * @return Nomenclador|null
     */
    public function getSaca(): ?Saca
    {
        return $this->saca;
    }

    /**
     * @param Saca|null $saca
     */
    public function setSaca(?Saca $saca): void
    {
        $this->saca = $saca;
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
     * @return Localizacion
     */
    public function getProvincia(): Localizacion
    {
        return $this->provincia;
    }

    /**
     * @param Localizacion $provincia
     */
    public function setProvincia(Localizacion $provincia): void
    {
        $this->provincia = $provincia;
    }

    /**
     * @return Localizacion
     */
    public function getMunicipio(): Localizacion
    {
        return $this->municipio;
    }

    /**
     * @param Localizacion $municipio
     */
    public function setMunicipio(Localizacion $municipio): void
    {
        $this->municipio = $municipio;
    }

    /**
     * @return Factura|null
     */
    public function getFactura(): ?Factura
    {
        return $this->factura;
    }

    /**
     * @param Factura|null $factura
     */
    public function setFactura(?Factura $factura): void
    {
        $this->factura = $factura;
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
