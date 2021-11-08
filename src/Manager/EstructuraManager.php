<?php

namespace App\Manager;

use App\Repository\EstructuraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EstructuraManager extends _Manager_
{
    public function __construct(
        private EstructuraRepository $estructuraRepository
    )
    {
    }

    public function obtenerNuestrosMejoresClientes(): Collection
    {
        $qb = $this->estructuraRepository
            ->createQueryBuilder('e')
            ->where('e.level IN (:level)')->setParameter('level', [0, 1, 2])
            ->andWhere('e.habilitado = :habilitado')->setParameter('habilitado', true)
            ->setMaxResults(6);

        $result = $qb->getQuery()->getResult();
        return new ArrayCollection($result);
    }
}