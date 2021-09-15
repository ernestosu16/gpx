<?php

namespace App\Config\Nomenclador;

use App\Entity\Nomenclador;

final class Envio extends _Nomenclador_
{
    static function parent(): ?string
    {
        return App::class;
    }

    static function code(): string
    {
        return 'ENVIO';
    }

    static function name(): string
    {
        return 'Envío';
    }

    static function description(): string
    {
        return 'Datos configuración del modulo de Envío';
    }

    static function discriminator(): string
    {
        return Nomenclador::class;
    }
}
