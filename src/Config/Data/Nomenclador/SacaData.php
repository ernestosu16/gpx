<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Nomenclador;

final class SacaData extends _Data_
{
    static function parent(): ?string
    {
        return AppData::class;
    }

    static function code(): string
    {
        return 'SACA';
    }

    static function name(): string
    {
        return 'Saca';
    }

    static function description(): string
    {
        return 'Nomencladores de saca';
    }

    static function discriminator(): string
    {
        return Nomenclador::class;
    }
}
