<?php

namespace App\Repository;

use App\Config\Data\Nomenclador\CanalData;
use App\Config\Data\Nomenclador\GrupoData;
use App\Entity\Canal;
use App\Entity\Nomenclador;
use Doctrine\ORM\QueryBuilder;

final class CanalRepository extends NomencladorRepository
{
    protected static function classEntity(): string
    {
        return Canal::class;
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        $parent = $this->findOneByCodigo(CanalData::code());
        $query = parent::createQueryBuilder($alias, $indexBy);
        $query->where($alias . '.parent = :parent')
            ->setParameter('parent', $parent->getId())
            ->orderBy($alias . '.lft', 'ASC');;
        return $query;
    }

    public function findByCodigoAduana(string $code): Nomenclador
    {
        $parent = $this->findOneByCodigo(CanalData::code());
        $canal = $this->findOneBy(['parent' => $parent, 'descripcion' => $code]);

        return $canal;
    }
}
