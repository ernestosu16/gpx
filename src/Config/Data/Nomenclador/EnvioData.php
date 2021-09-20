<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Nomenclador;

final class EnvioData extends _Data_
{
    static function parent(): ?string
    {
        return AppData::class;
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
