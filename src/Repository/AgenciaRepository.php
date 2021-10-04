<?php

namespace App\Repository;

use App\Config\Data\Nomenclador\AgenciaData;
use App\Entity\Agencia;
use Doctrine\ORM\QueryBuilder;

final class AgenciaRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return Agencia::class;
    }

    public function findAll()
    {
        return $this->createQueryBuilder('q')
            ->where('q.parent is not null')->getQuery()->getResult();
    }
}
