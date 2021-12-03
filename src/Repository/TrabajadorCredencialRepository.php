<?php

namespace App\Repository;

use App\Entity\TrabajadorCredencial;

final class TrabajadorCredencialRepository extends _Repository_
{
    protected static function classEntity(): string
    {
        return TrabajadorCredencial::class;
    }

    public function findOneByUsuario(string $usuario): ?TrabajadorCredencial
    {
        return $this->findOneBy(['usuario' => $usuario]);
    }
}
