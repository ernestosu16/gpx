<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\AgenciaData;
use App\Entity\Agencia;
use App\Form\Admin\AgenciaType;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/agencia', name: 'admin_agencia')]
final class AgenciaController extends _CrudController_
{
    protected static function entity(): string
    {
        return Agencia::class;
    }

    protected static function formType(): string
    {
        return AgenciaType::class;
    }

    #[Pure] protected static function parentCode(): ?string
    {
        return AgenciaData::code();
    }

    protected static function fields(): array
    {
        return ['nombre', 'descripcion', 'habilitado'];
    }

    protected static function config(): array
    {
        return [
            'titles' => [
                self::INDEX => 'Listado de las agencias',
                self::NEW => 'Nueva agencia',
                self::EDIT => 'Editar agencia',
            ],
            'filter' => [
                'q.nombre' => 'nombre'
            ],
            'routes' => [
                self::INDEX => 'admin_agencia_index',
                self::NEW => 'admin_agencia_new',
                self::EDIT => 'admin_agencia_edit',
                self::DELETE => 'admin_agencia_delete',
            ]
        ];
    }

}
