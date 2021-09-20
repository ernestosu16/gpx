<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\EstructuraTipo;

final class EstructuraTipoData extends _Data_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return 'ESTRUCTURA_TIPO';
    }

    static function name(): string
    {
        return 'Tipo de estructura';
    }

    static function description(): string
    {
        return 'Tipos de estructura';
    }

    static function discriminator(): string
    {
        return EstructuraTipo::class;
    }
}
