<?php

namespace App\Repository;

use App\Entity\Grupo;

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
}
