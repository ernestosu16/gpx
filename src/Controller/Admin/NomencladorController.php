<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\AppData;
use App\Entity\Nomenclador;
use App\Form\Admin\NomencladorType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/nomenclador', name: 'admin_nomenclador')]
final class NomencladorController extends CrudTreeNomencladorController
{
    protected static function parent(): AppData
    {
        return AppData::newInstance();
    }

    protected static function entity(): string
    {
        return Nomenclador::class;
    }

    protected static function formType(): string
    {
        return NomencladorType::class;
    }

    protected static function config(): array
    {
        return [
            'title' => [
                self::INDEX => 'Lista de nomencladores',
                self::NEW => 'Nuevo nomenclador',
                self::EDIT => 'Editar nomenclador',
                self::SHOW => 'Mostrar nomenclador',
            ],
            'routes' => [
                self::INDEX => 'admin_nomenclador_index',
                self::NEW => 'admin_nomenclador_new',
                self::EDIT => 'admin_nomenclador_edit',
                self::SHOW => 'admin_nomenclador_show',
                self::DELETE => 'admin_nomenclador_delete',
                self::MOVE_UP => 'admin_nomenclador_move_up',
                self::MOVE_DOWN => 'admin_nomenclador_move_down',
            ],
        ];
    }
}
