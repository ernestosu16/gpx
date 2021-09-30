<?php

namespace App\Repository;

use App\Config\Data\Nomenclador\GrupoData;
use App\Entity\Grupo;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Grupo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grupo|null findOneBy(array $criteria, array $orderBy = null)
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
        $parent = $this->findOneByCodigo(GrupoData::code());
        $query = parent::createQueryBuilder($alias, $indexBy);
        $query->where($alias . '.parent = :parent')
            ->setParameter('parent', $parent)
            ->orderBy($alias . '.lft', 'ASC');;
        return $query;
    }

    /**
     * @return Grupo[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('g')->getQuery()->getResult();
    }
}
