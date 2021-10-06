<?php

namespace App\Repository;

use App\Entity\EstructuraTipo;
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
            ->where($alias . '.parent is not null')
            ->orderBy($alias . '.lft', 'ASC');
    }
}
