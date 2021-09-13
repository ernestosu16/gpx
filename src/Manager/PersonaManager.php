<?php

namespace App\Manager;

use App\Entity\Persona;
use JetBrains\PhpStorm\Pure;

class PersonaManager
{
    #[Pure] public function generarHash(Persona $persona): string
    {
        $index = sprintf(
            '%s-%s-%s',
            $persona->getNombre(),
            $persona->getApellidoPrimero(),
            $persona->getApellidoSegundo()
        );

        return md5($index);
    }
}
