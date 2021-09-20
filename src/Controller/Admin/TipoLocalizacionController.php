<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\LocalizacionTipoData;
use App\Entity\LocalizacionTipo;
use App\Form\Admin\LocalizacionTipoType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tipo/localizacion', name: 'admin_tipo_localizacion')]
final class TipoLocalizacionController extends CrudTreeNomencladorController
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
                self::INDEX => 'admin_tipo_localizacion_index',
                self::NEW => 'admin_tipo_localizacion_new',
                self::EDIT => 'admin_tipo_localizacion_edit',
                self::SHOW => 'admin_tipo_localizacion_show',
                self::DELETE => 'admin_tipo_localizacion_delete',
                self::MOVE_UP => 'admin_tipo_localizacion_move_up',
                self::MOVE_DOWN => 'admin_tipo_localizacion_move_down',
            ],
        ];
    }

    static protected function parent(): LocalizacionTipoData
    {
        return LocalizacionTipoData::newInstance();
    }

}
