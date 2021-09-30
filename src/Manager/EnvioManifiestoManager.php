<?php


namespace App\Manager;

use App\Entity\EnvioManifiesto;
use Doctrine\ORM\EntityManagerInterface;

class EnvioManifiestoManager extends _Manager_
{
    /**
     * @var EntityManagerInterface
     */
    protected $doctrineEntityManager;

    public function createEnvioManifiesto($envioManifiesto)
    {
        /** @var $em EntityManagerInterface **/
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($envioManifiesto);
        $em->flush();
        //dump('took');exit;
        //$this->get
        return $envioManifiesto;
    }

}