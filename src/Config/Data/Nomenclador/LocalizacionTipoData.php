<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Nomenclador\LocalizacionTipo;

final class LocalizacionTipoData extends _Data_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return 'TIPO_LOCALIZACION';
    }

    static function name(): string
    {
        return 'Tipo de localización';
    }

    static function description(): string
    {
        return 'Tipos de localizaciones';
    }

    static function discriminator(): string
    {
        return LocalizacionTipo::class;
    }
}
