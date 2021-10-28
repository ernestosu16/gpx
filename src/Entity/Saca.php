<?php

namespace App\Entity;

use App\Repository\SacaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: SacaRepository::class)]
class Saca extends _Entity_
{
    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    private string $sello;

    #[ORM\Column(type: 'datetime',nullable: false)]
    private \DateTime $fecha_creacion;

    #[ORM\Column(type: 'string', length: 31, nullable: false)]
    private string $codigo;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $peso;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(name: 'estado_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $estado;

    #[ORM\ManyToOne(targetEntity: Factura::class)]
    #[ORM\JoinColumn(name: 'factura_id', referencedColumnName: 'id', nullable: true)]
    private ?Factura $factura;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Estructura $origen;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Estructura $destino;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Nomenclador $tipo_embalaje;

    #[ORM\OneToMany(mappedBy: 'saca', targetEntity: Envio::class, cascade: ['persist'])]
    private ?Collection $envios;

    #[ORM\Column(type: 'json', nullable: true )]
    private $observaciones;

    #[ORM\OneToMany(mappedBy: "saca", targetEntity: SacaTraza::class)]
    private $trazas;
    
    #[Pure]
    public function __construct()
    {
        $this->envios = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSello(): string
    {
        return $this->sello;
    }

    /**
     * @param string $sello
     * @return Saca
     */
    public function setSello(string $sello): Saca
    {
        $this->sello = $sello;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    /**
     * @param mixed $fecha_creacion
     * @return Saca
     */
    public function setFechaCreacion($fecha_creacion)
    {
        $this->fecha_creacion = $fecha_creacion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     * @return Saca
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param mixed $peso
     * @return Saca
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     * @return Saca
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * @param mixed $factura
     * @return Saca
     */
    public function setFactura($factura)
    {
        $this->factura = $factura;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * @param mixed $origen
     * @return Saca
     */
    public function setOrigen($origen)
    {
        $this->origen = $origen;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * @param mixed $destino
     * @return Saca
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoEmbalaje()
    {
        return $this->tipo_embalaje;
    }

    /**
     * @param mixed $tipo_embalaje
     * @return Saca
     */
    public function setTipoEmbalaje($tipo_embalaje)
    {
        $this->tipo_embalaje = $tipo_embalaje;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param mixed $observaciones
     * @return Saca
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
        return $this;
    }

    /**
     * @return Collection|SacaTraza[]
     */
    public function getTrazas(): Collection
    {
        return $this->trazas;
    }

    public function addTraza(SacaTraza $traza): self
    {
        if (!$this->trazas->contains($traza)) {
            $this->trazas[] = $traza;
            $traza->setSaca($this);
        }

        return $this;
    }

    public function removeTraza(SacaTraza $traza): self
    {
        if ($this->trazas->removeElement($traza)) {
            // set the owning side to null (unless already changed)
            if ($traza->getSaca() === $this) {
                $traza->setSaca(null);
            }
        }

        return $this;
    }

}
