<?php

namespace App\Entity;

use App\Repository\EstructuraTipoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstructuraTipoRepository::class)]
class EstructuraTipo extends Nomenclador
{
}
