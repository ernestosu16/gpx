<?php

namespace App\Entity\Envio;

use App\Entity\_Entity_;
use App\Entity\Estructura;
use App\Repository\SacaConsecutivoRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: SacaConsecutivoRepository::class)]
class SacaConsecutivo extends _Entity_
{
    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Estructura $oficina_origen;

    #[ORM\ManyToOne(targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Estructura $oficina_destino;

    #[ORM\Column(type: 'datetime', length: 4, nullable: false)]
    private DateTime $anno;

    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    private int $numero;

    #[Pure]
    public function __construct()
    {

    }

    public function getOficinaOrigen(): Estructura
    {
        return $this->oficina_origen;
    }

    public function setOficinaOrigen(Estructura $oficina_origen): SacaConsecutivo
    {
        $this->oficina_origen = $oficina_origen;
        return $this;
    }

    public function getOficinaDestino(): Estructura
    {
        return $this->oficina_destino;
    }

    public function setOficinaDestino(Estructura $oficina_destino): SacaConsecutivo
    {
        $this->oficina_destino = $oficina_destino;
        return $this;
    }

    public function getAnno(): DateTime
    {
        return $this->anno;
    }

    public function setAnno(DateTime $anno): SacaConsecutivo
    {
        $this->anno = $anno;
        return $this;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): SacaConsecutivo
    {
        $this->numero = $numero;
        return $this;
    }


}
