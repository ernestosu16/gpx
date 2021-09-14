<?php

namespace App\Repository;

use App\Entity\Trabajador;
use Doctrine\ORM\NonUniqueResultException;

final class TrabajadorRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return Trabajador::class;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByNumeroIdentidad(?string $numeroIdentidad): ?Trabajador
    {
        return $this->createQueryBuilder('trabajador')
            ->join('trabajador.persona', 'persona')
            ->where('persona.numero_identidad = :numeroIdentidad')
            ->setParameter('numeroIdentidad', $numeroIdentidad)
            ->getQuery()->getOneOrNullResult();
    }
}
