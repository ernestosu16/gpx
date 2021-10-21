<?php

namespace App\Entity;

use App\Repository\SacaTrazaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:SacaTrazaRepository::class)]
class SacaTraza
{
    private $fecha;
    private $peso;
    private $estado;
    private $saca;
    private $factura;
    private $ip;
    private $trabajador;
    private $estructura;
}
