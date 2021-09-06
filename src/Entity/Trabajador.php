<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sip_trabajador")]
class Trabajador extends _Entity_
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36)]
    #[ORM\GeneratedValue(strategy: "UUID")]
    private string $id;

    #[ORM\OneToOne(mappedBy: Persona::class)]
    #[ORM\JoinColumn(name: 'persona_id', referencedColumnName: 'id')]
    private Persona $persona;

    #[ORM\Column(type: "string", length: 11)]
    private string $cargo;

    #[ORM\Column(type: "boolean")]
    private bool $habilitado = true;
}
