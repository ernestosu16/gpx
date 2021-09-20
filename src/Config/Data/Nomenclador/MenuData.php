<?php

namespace App\Config\Data\Nomenclador;

use App\Config\Data\_Data_;
use App\Entity\Menu;

final class MenuData extends _Data_
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
        return Menu::class;
    }
}
