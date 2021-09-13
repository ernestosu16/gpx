<?php

namespace App\Config\Nomenclador;

use App\Entity\Grupo as GrupoEntity;

final class Grupo extends _Nomenclador_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return 'GRUPO';
    }

    static function name(): string
    {
        return 'Grupos de Usuarios';
    }

    static function description(): string
    {
        return 'Listado de los grupos de la aplicación';
    }

    static function discriminator(): string
    {
        return GrupoEntity::class;
    }
}
