<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Nomenclador;

final class AppData extends _Data_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return 'APP';
    }

    static function name(): string
    {
        return 'Aplicación';
    }

    static function description(): string
    {
        return 'Datos de la aplicación';
    }

    static function discriminator(): string
    {
        return Nomenclador::class;
    }
}
