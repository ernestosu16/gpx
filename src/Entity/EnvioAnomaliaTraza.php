<?php

namespace App\Entity;

use App\Repository\EnvioAnomaliaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvioAnomaliaTraza::class)]
class EnvioAnomaliaTraza extends _Entity_
{
    #[ORM\Column(type: 'string', length: 2550, nullable: false)]
    private string $descripcion;

    #[ORM\ManyToOne(targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'anomalia_id', referencedColumnName: 'id', nullable: false)]
    private Nomenclador $anomalia;

    #[ORM\ManyToOne(targetEntity: EnvioTraza::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'envio_traza_id', referencedColumnName: 'id', nullable: false)]
    private EnvioTraza $envio_traza;

}
