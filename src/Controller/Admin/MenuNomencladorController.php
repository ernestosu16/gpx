<?php

namespace App\Controller\Admin;

use App\Config\Nomenclador\Menu as MenuNomenclador;
use App\Entity\Menu;
use App\Form\Admin\MenuType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/menu', name: 'admin_menu')]
final class MenuNomencladorController extends CrudTreeNomencladorController
{
    protected static function parent(): MenuNomenclador
    {
        return MenuNomenclador::newInstance();
    }

    protected static function entity(): string
    {
        return Menu::class;
    }

    protected static function formType(): string
    {
        return MenuType::class;
    }

    protected static function config(): array
    {
        return [
            'title' => [
                self::INDEX => 'Lista de menú',
                self::NEW => 'Nuevo menú',
                self::EDIT => 'Editar menú',
                self::SHOW => 'Mostrar menú',
            ],
            'routes' => [
                self::INDEX => 'admin_menu_index',
                self::NEW => 'admin_menu_new',
                self::EDIT => 'admin_menu_edit',
                self::SHOW => 'admin_menu_show',
                self::DELETE => 'admin_menu_delete',
            ],
        ];
    }
}
