<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Nomenclador\Grupo;

final class GrupoData extends _Data_
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
        return Grupo::class;
    }
}
