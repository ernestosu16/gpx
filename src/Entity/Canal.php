<?php

namespace App\Entity;

use App\Repository\CanalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CanalRepository::class)]
#[ORM\Cache]
class Canal extends Nomenclador
{
}
