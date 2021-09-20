<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\LocalizacionTipoData;
use App\Entity\LocalizacionTipo;
use App\Form\Admin\LocalizacionTipoType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tipo/localizacion', name: 'admin_localizacion_tipo')]
final class LocalizacionTipoController extends CrudTreeNomencladorController
{
    protected static function entity(): string
    {
        return LocalizacionTipo::class;
    }

    protected static function formType(): string
    {
        return LocalizacionTipoType::class;
    }

    protected static function config(): array
    {
        return [
            'title' => [
                self::INDEX => 'Lista de localizaciones',
                self::NEW => 'Nueva localización',
                self::EDIT => 'Editar localización',
                self::SHOW => 'Mostrar localización',
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

    static protected function parent(): LocalizacionTipoData
    {
        return LocalizacionTipoData::newInstance();
    }

}
