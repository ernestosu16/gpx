<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\EstructuraTipoData;
use App\Entity\EstructuraTipo;
use App\Form\Admin\EstructuraTipoType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tipo/estructura', name: 'admin_estructura_tipo')]
class EstructuraTipoController extends CrudTreeNomencladorController
{
    protected static function entity(): string
    {
        return EstructuraTipo::class;
    }

    protected static function formType(): string
    {
        return EstructuraTipoType::class;
    }

    protected static function config(): array
    {
        return [
            'title' => [
                self::INDEX => 'Lista de tipos estructuras',
                self::NEW => 'Nueva  tipo estructura',
                self::EDIT => 'Editar  tipo estructura',
                self::SHOW => 'Mostrar  tipo estructura',
            ],
            'routes' => [
                self::INDEX => 'admin_localizacion_tipo_index',
                self::NEW => 'admin_localizacion_tipo_new',
                self::EDIT => 'admin_localizacion_tipo_edit',
                self::SHOW => 'admin_localizacion_tipo_show',
                self::DELETE => 'admin_localizacion_tipo_delete',
                self::MOVE_UP => 'admin_localizacion_tipo_move_up',
                self::MOVE_DOWN => 'admin_localizacion_tipo_move_down',
            ],
        ];
    }

    static protected function parent(): EstructuraTipoData
    {
        return EstructuraTipoData::newInstance();
    }

}

