<?php

namespace App\Repository;

use App\Entity\Nomenclador\EstructuraTipo;
use Doctrine\ORM\QueryBuilder;

final class EstructuraTipoRepository extends NomencladorRepository
{
    protected static function classEntity(): string
    {
        return EstructuraTipo::class;
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        return parent::createQueryBuilder($alias, $indexBy)
            ->orderBy($alias . '.lft', 'ASC');
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.parent is not null')
            ->getQuery()
            ->getResult();
    }
}
