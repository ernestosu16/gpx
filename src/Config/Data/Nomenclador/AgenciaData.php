<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Agencia;

final class AgenciaData extends _Data_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return 'AGENCIA';
    }

    static function name(): string
    {
        return 'Agencias';
    }

    static function description(): string
    {
        return 'Agencias o curris';
    }

    static function discriminator(): string
    {
        return Agencia::class;
    }
}
