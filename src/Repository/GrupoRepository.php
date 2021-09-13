<?php

namespace App\Repository;

use App\Config\Nomenclador\Grupo as GrupoNomenclador;
use App\Entity\Grupo;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Grupo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grupo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grupo[]    findAll()
 * @method Grupo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class GrupoRepository extends NomencladorRepository
{
    protected static function classEntity(): string
    {
        return Grupo::class;
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder
    {
        $parent = $this->findOneByCodigo(GrupoNomenclador::code());
        $query = parent::createQueryBuilder($alias, $indexBy);
        $query->where($alias . '.parent = :parent')
            ->setParameter('parent', $parent)
            ->orderBy($alias . '.nombre', 'ASC');;
        return $query;
    }
}
