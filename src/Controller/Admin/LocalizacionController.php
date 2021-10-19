<?php

namespace App\Controller\Admin;

use App\Entity\Localizacion;
use App\Form\Admin\LocalizacionType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/localizacion', name: 'admin_localizacion')]
final class LocalizacionController extends _CrudController_
{
    protected static function entity(): string
    {
        return Localizacion::class;
    }

    protected static function formType(): string
    {
        return LocalizacionType::class;
    }

    protected static function fields(): array
    {
        return ['nombre', 'codigo_aduana', 'tipo'];
    }

    protected static function config(): array
    {
        return [
            'filter' => [
                'q.nombre' => 'nombre',
                'q.codigo_aduana' => 'codigo_aduana',
            ],
            'routes' => [
                self::INDEX => 'admin_localizacion_index',
                self::NEW => 'admin_localizacion_new',
                self::EDIT => 'admin_localizacion_edit',
//                self::DELETE => 'admin_localizacion_delete',
            ],
        ];
    }
}
