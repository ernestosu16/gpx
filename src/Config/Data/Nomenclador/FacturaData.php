<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Nomenclador;

final class FacturaData extends _Data_
{
    static function parent(): ?string
    {
        return AppData::class;
    }

    static function code(): string
    {
        return 'FACTURA';
    }

    static function name(): string
    {
        return 'Factura';
    }

    static function description(): string
    {
        return 'Nomencladores de facturas';
    }

    static function discriminator(): string
    {
        return Nomenclador::class;
    }
}
