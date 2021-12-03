<?php

namespace App\Repository;

use App\Entity\Estructura;

final class EstructuraRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return Estructura::class;
    }

    public function findAll(): array
    {
        return $this->findBy([], ['lft' => 'ASC']);
    }

    /**
     * @return Estructura[] Returns an array of EnvioAduana objects
     */
    public function findEstructuraByTipo(string $tipo)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.tipos', 't')
            ->andWhere('t.codigo = :val')
            ->setParameter('val', $tipo)
            ->getQuery()
            ->getResult();
    }
}
