<?php

namespace App\Config\Nomenclador;

use App\Entity\Menu as MenuEntity;

final class Menu extends _Nomenclador_
{
    static function parent(): ?string
    {
        return null;
    }

    static function code(): string
    {
        return 'MENU';
    }

    static function name(): string
    {
        return 'Menú';
    }

    static function description(): string
    {
        return 'Listado de Menú de la aplicación';
    }

    static function discriminator(): string
    {
        return MenuEntity::class;
    }
}
