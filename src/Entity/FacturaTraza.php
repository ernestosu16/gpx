<?php

namespace App\Entity;

use App\Repository\FacturaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:FacturaTrazaRepository::class)]
class FacturaTraza extends _Entity_
{
private $fecha;
private $estado;
private $factura;
private $ip;
private $trabajador;
private $estructura;

}
