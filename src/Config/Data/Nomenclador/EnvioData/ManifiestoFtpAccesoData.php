<?php

namespace App\Config\Data\Nomenclador\EnvioData;

use App\Config\Data\_Data_;
use App\Config\Data\Nomenclador\EnvioData;
use App\Entity\Nomenclador;

final class ManifiestoFtpAccesoData extends _Data_
{
    static function parent(): ?string
    {
        return EnvioData::class;
    }

    static function code(): string
    {
        return 'MANIFIESTO_FTP_ACCESO';
    }

    static function name(): string
    {
        return 'FTP Manifiesto';
    }

    static function description(): string
    {
        return 'Datos del acceso al FTP';
    }

    static function discriminator(): string
    {
        return Nomenclador::class;
    }

}
