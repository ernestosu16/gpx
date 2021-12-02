<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\LocalizacionTipoData;
use App\Entity\Nomenclador\LocalizacionTipo;
use App\Form\Admin\LocalizacionTipoType;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tipo/localizacion', name: 'admin_localizacion_tipo')]
final class LocalizacionTipoController extends _CrudController_
{
    protected static function entity(): string
    {
        return LocalizacionTipo::class;
    }

    protected static function formType(): string
    {
        return LocalizacionTipoType::class;
    }

    #[Pure] protected static function parentCode(): ?string
    {
        return LocalizacionTipoData::code();
    }

    protected static function fields(): array
    {
        return ['nombre', 'descripcion', 'habilitado'];
    }

    protected static function config(): array
    {
        return [
            'titles' => [
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
            ],
        ];
    }
}
    /* extends _CrudController_
{
    protected static function entity(): string
    {
        return LocalizacionTipo::class;
    }

    protected static function formType(): string
    {
        return LocalizacionTipoType::class;
    }

    #[Pure] protected static function parentCode(): ?string
    {
        return LocalizacionTipoData::code();
    }

    protected static function fields(): array
    {
        return ['nombre', 'descripcion', 'habilitado'];
    }

    protected static function config(): array
    {
        return [
            'titles' => [
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
            ],
        ];
    }
}*/
