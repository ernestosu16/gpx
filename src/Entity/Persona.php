<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sip_persona")]
#[ORM\Index(fields: ['numero_identidad'], name: 'IDX_NUMERO_IDENTIDAD')]
class Persona extends _Entity_
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36)]
    #[ORM\GeneratedValue(strategy: "UUID")]
    private string $id;

    #[ORM\Column(type: "string", length: 11, unique: true)]
    private string $numero_identidad;

    #[ORM\Column(type: "string", length: 100)]
    private string $nombre;

    #[ORM\Column(type: "string", length: 100)]
    private string $apellido_primero;

    #[ORM\Column(type: "string", length: 100)]
    private string $apellido_segundo;
}
