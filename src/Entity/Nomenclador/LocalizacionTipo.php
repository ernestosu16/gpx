<?php

namespace App\Entity\Nomenclador;

use App\Entity\Nomenclador;
use App\Repository\LocalizacionTipoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocalizacionTipoRepository::class)]
#[ORM\Cache]
class LocalizacionTipo extends Nomenclador
{
    const PROVINCIA = 'PROVINCIA';
    const MUNICIPIO = 'MUNICIPIO';
    const AREA = 'AREA';
    const REPARTO = 'REPARTO';
    const BARRIO = 'BARRIO';
    const CUADRA = 'CUADRA';
}
