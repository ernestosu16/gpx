<?php

namespace App\Repository;

use App\Entity\Estructura;

final class EstructuraRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return Estructura::class;
    }

    public function findAll(): array
    {
        return $this->findBy([], ['lft' => 'ASC']);
    }
}
