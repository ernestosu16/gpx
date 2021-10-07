<?php

namespace App\Entity;

use App\Repository\AnomaliaEnvioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnomaliaEnvioRepository::class)]
class AnomaliaEnvio extends Anomalia
{

    #[ORM\ManyToOne(targetEntity: Envio::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $envio;

    public function getEnvio(): ?Envio
    {
        return $this->envio;
    }

    public function setEnvio(?Envio $envio): self
    {
        $this->envio = $envio;

        return $this;
    }
}
