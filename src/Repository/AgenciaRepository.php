<?php

namespace App\Repository;

use App\Config\Data\Nomenclador\AgenciaData;
use App\Config\Data\Nomenclador\GrupoData;
use App\Entity\Agencia;
use Doctrine\ORM\QueryBuilder;

final class AgenciaRepository extends NomencladorRepository
{
    protected static function classEntity(): string
    {
        return Agencia::class;
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        $parent = $this->findOneByCodigo(AgenciaData::code());
        $query = parent::createQueryBuilder($alias, $indexBy);
        $query->where($alias . '.parent = :parent')
            ->setParameter('parent', $parent->getId())
            ->orderBy($alias . '.lft', 'ASC');;
        return $query;
    }
}
