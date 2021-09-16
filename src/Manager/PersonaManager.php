<?php

namespace App\Manager;

use App\Entity\Persona;
use JetBrains\PhpStorm\Pure;

class PersonaManager extends _Manager_
{
    #[Pure] public function generarHash(Persona $persona): string
    {
        $index = [];

        if ($persona->getNumeroIdentidad())
            $index[] = $persona->getNumeroIdentidad();

        if ($persona->getNumeroPasaporte())
            $index[] = $persona->getNumeroPasaporte();

        $index[] = $persona->getNombre();
        $index[] = $persona->getApellidoPrimero();
        $index[] = $persona->getApellidoSegundo();

        return md5(implode('-', $index));
    }
}
