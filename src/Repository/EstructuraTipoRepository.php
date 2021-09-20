<?php

namespace App\Repository;

use App\Entity\EstructuraTipo;

final class EstructuraTipoRepository extends NomencladorRepository
{
    protected static function classEntity(): string
    {
        return EstructuraTipo::class;
    }
}
