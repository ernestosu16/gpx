<?php

namespace App\Entity;

use App\Repository\AgenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgenciaRepository::class)]
#[ORM\Cache]
class Agencia extends Nomenclador
{
}
