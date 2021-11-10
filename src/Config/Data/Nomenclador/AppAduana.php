<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Nomenclador;

class AppAduana extends _Data_
{
    static function parent(): ?string
    {
        return AppData::class;
    }

    static function code(): string
    {
        return 'ADUANA';
    }

    static function name(): string
    {
        return 'Aduana';
    }

    static function description(): string
    {
        return 'Datos de la Aduana.';
    }

    static function discriminator(): string
    {
        return Nomenclador::class;
    }
}