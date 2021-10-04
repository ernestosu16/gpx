<?php

namespace App\Controller\Admin;

use App\Entity\Pais;
use App\Form\Admin\PaisType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/pais', name: 'admin_pais')]
final class PaisController extends _CrudController_
{
    protected static function entity(): string
    {
        return Pais::class;
    }

    protected static function formType(): string
    {
        return PaisType::class;
    }

    protected static function config(): array
    {
        return [
            'routes' => [
                self::INDEX => 'admin_pais_index',
                self::NEW => 'admin_pais_new',
                self::EDIT => 'admin_pais_edit',
                self::DELETE => 'admin_pais_delete',
            ]
        ];
    }
}
