<?php

namespace App\Config\Nomenclador;

use App\Entity\Nomenclador;

final class App extends _Nomenclador_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return Nomenclador::ROOT;
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
