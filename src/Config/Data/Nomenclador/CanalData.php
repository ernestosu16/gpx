<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Canal;

final class CanalData extends _Data_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return 'CANAL';
    }

    static function name(): string
    {
        return 'Canales';
    }

    static function description(): string
    {
        return 'Canales de aduana';
    }

    static function discriminator(): string
    {
        return Canal::class;
    }
}
