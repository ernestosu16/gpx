<?php

namespace App\Entity;

use App\Repository\FacturaConsecutivoRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: FacturaConsecutivoRepository::class)]
class FacturaConsecutivo extends _Entity_
{
    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Estructura $oficina_origen;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Estructura $oficina_destino;

    #[ORM\Column(type: 'datetime', length: 4, nullable: false)]
    private \DateTime $anno;

    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    private string $numero;

    #[Pure]
    public function __construct()
    {

    }

    /**
     * @return Estructura
     */
    public function getOficinaOrigen(): Estructura
    {
        return $this->oficina_origen;
    }

    /**
     * @param Estructura $oficina_origen
     * @return FacturaConsecutivo
     */
    public function setOficinaOrigen(Estructura $oficina_origen): FacturaConsecutivo
    {
        $this->oficina_origen = $oficina_origen;
        return $this;
    }

    /**
     * @return Estructura
     */
    public function getOficinaDestino(): Estructura
    {
        return $this->oficina_destino;
    }

    /**
     * @param Estructura $oficina_destino
     * @return FacturaConsecutivo
     */
    public function setOficinaDestino(Estructura $oficina_destino): FacturaConsecutivo
    {
        $this->oficina_destino = $oficina_destino;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAnno(): \DateTime
    {
        return $this->anno;
    }

    /**
     * @param \DateTime $anno
     * @return FacturaConsecutivo
     */
    public function setAnno(\DateTime $anno): FacturaConsecutivo
    {
        $this->anno = $anno;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumero(): string
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     * @return FacturaConsecutivo
     */
    public function setNumero(string $numero): FacturaConsecutivo
    {
        $this->numero = $numero;
        return $this;
    }

}
