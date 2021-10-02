<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\EstructuraTipoData;
use App\Entity\EstructuraTipo;
use App\Form\Admin\EstructuraTipoType;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tipo/estructura', name: 'admin_estructura_tipo')]
final class EstructuraTipoController extends _CrudController_
{
    protected static function entity(): string
    {
        return EstructuraTipo::class;
    }

    protected static function formType(): string
    {
        return EstructuraTipoType::class;
    }

    #[Pure] protected static function parentCode(): ?string
    {
        return EstructuraTipoData::code();
    }

    protected static function fields(): array
    {
        return ['nombre', 'descripcion', 'habilitado'];
    }

    protected static function config(): array
    {
        return [
            'titles' => [
                self::INDEX => 'Lista de tipos estructuras',
                self::NEW => 'Nueva  tipo estructura',
                self::EDIT => 'Editar  tipo estructura',
                self::SHOW => 'Mostrar  tipo estructura',
            ],
            'routes' => [
                self::INDEX => 'admin_estructura_tipo_index',
                self::NEW => 'admin_estructura_tipo_new',
                self::EDIT => 'admin_estructura_tipo_edit',
                self::SHOW => 'admin_estructura_tipo_show',
                self::DELETE => 'admin_estructura_tipo_delete',
            ],
        ];
    }
}

