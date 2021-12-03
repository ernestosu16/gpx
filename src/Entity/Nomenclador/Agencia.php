<?php

namespace App\Entity\Nomenclador;

use App\Entity\Nomenclador;
use App\Repository\Nomenclador\AgenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgenciaRepository::class)]
#[ORM\Cache]
class Agencia extends Nomenclador
{
}
