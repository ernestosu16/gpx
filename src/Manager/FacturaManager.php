<?php


namespace App\Manager;


use App\Entity\Factura;
use App\Entity\FacturaTraza;
use App\Entity\TrabajadorCredencial;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class FacturaManager extends _Manager_
{
    private EntityManagerInterface $entityManager;

    /**
     * @throws ORMException
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $doctrineEntityManager
    )
    {
        $this->entityManager = $doctrineEntityManager;
    }

    public function createTraza(Factura $factura, TrabajadorCredencial $user)
    {
        $traza = new FacturaTraza();
        $traza->setEstado($factura->getEstado());
        $traza->setEstructura($user->getTrabajador()->getEstructura());
        $traza->setFactura($factura);
        $traza->setFecha(new \DateTime());
        $ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $traza->setIp($ip);
        $traza->setTrabajador($user->getTrabajador());

        $this->entityManager->persist($traza);
        $this->entityManager->flush();
    }
}