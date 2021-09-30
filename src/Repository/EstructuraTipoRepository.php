<?php

namespace App\Repository;

use App\Entity\EstructuraTipo;

final class EstructuraTipoRepository extends NomencladorRepository
{
    protected static function classEntity(): string
    {
        return EstructuraTipo::class;
    }

    /**
     * @return EstructuraTipo[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.parent is not null')->getQuery()->getResult();
    }
}
