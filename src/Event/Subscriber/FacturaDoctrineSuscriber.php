<?php


namespace App\Event\Subscriber;


use App\Entity\Factura;
use App\Entity\FacturaTraza;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class FacturaDoctrineSuscriber extends _DoctrineSubscriber_
{

    public function getSubscribedEvents()
    {
        return [Events::postFlush];
    }

    public function postFlush(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Factura)
            return;

        $traza = new FacturaTraza();
        $traza->setEstado();
        $traza->setEstructura();
        $traza->setFactura();
        $traza->setFecha();
        $traza->setIp();
        $traza->setTrabajador();

        $repository = $this->get();
        $repository->persist($traza);
        $repository->flush();
    }
}