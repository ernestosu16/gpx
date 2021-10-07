<?php

namespace App\Manager;

use App\Entity\Persona;
use Doctrine\ORM\EntityManagerInterface;
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

    public function createPersona($persona)
    {
        /** @var $em EntityManagerInterface **/
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($persona);
        //$em->flush();
        //dump('took');exit;
        //$this->get
        return $persona;
    }
}
