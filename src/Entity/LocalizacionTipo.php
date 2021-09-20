<?php

namespace App\Entity;

use App\Repository\LocalizacionTipoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocalizacionTipoRepository::class)]
class LocalizacionTipo extends Nomenclador
{
    const PROVINCIA = 'PROVINCIA';
    const MUNICIPIO = 'MUNICIPIO';
    const AREA = 'AREA';
    const REPARTO = 'REPARTO';
    const BARRIO = 'BARRIO';
    const CUADRA = 'CUADRA';
}