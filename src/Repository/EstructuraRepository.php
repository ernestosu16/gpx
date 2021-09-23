<?php

namespace App\Repository;

use App\Entity\Estructura;

class EstructuraRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return Estructura::class;
    }

}
