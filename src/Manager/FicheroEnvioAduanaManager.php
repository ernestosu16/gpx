<?php


namespace App\Manager;

use App\Entity\FicheroEnvioAduana;
use Doctrine\ORM\EntityManagerInterface;

class FicheroEnvioAduanaManager extends _Manager_
{
    public function createFicheroEnvioAduana(string $nombre_fichero, string $tipo_fichero)
    {
        /** @var $em EntityManagerInterface **/
        $em = $this->get('doctrine.orm.default_entity_manager');

        $ficheroEnvioAduana = new FicheroEnvioAduana($nombre_fichero, $tipo_fichero);

        $em->persist($ficheroEnvioAduana);
        $em->flush();
        return $ficheroEnvioAduana;
    }
}
