<?php

namespace App\Entity;

use App\Repository\AnomaliaFacturaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnomaliaFacturaRepository::class)]
class AnomaliaFactura extends Anomalia
{
    #[ORM\ManyToOne(targetEntity: Factura::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $factura;

    public function getFactura(): ?Factura
    {
        return $this->factura;
    }

    public function setFactura(?Factura $factura): self
    {
        $this->factura = $factura;

        return $this;
    }
}
