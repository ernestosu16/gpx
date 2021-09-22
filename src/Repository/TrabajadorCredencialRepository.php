<?php

namespace App\Repository;

use App\Entity\TrabajadorCredencial;

class TrabajadorCredencialRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return TrabajadorCredencial::class;
    }
}
