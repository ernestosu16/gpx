<?php

namespace App\Repository;

use App\Config\Data\Nomenclador\AgenciaData;
use App\Entity\Nomenclador\Agencia;
use App\Entity\Nomenclador;
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

    public function findByCodigoAduana(string $code): Nomenclador
    {
        $parent = $this->findOneByCodigo(AgenciaData::code());
        $agencia = $this->findOneBy(['parent' => $parent, 'descripcion' => $code]);

        return $agencia;
    }
}
