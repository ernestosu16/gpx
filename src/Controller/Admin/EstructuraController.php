<?php

namespace App\Controller\Admin;

use App\Entity\Estructura;
use App\Form\Admin\EstructuraType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/estructura', name: 'admin_estructura')]
final class EstructuraController extends _CrudController_
{
    protected static function entity(): string
    {
        return Estructura::class;
    }

    protected static function formType(): string
    {
        return EstructuraType::class;
    }

    protected static function config(): array
    {
        return [
            'titles' => [
                self::INDEX => 'Listado de las estructuras',
            ],
            'templates' => [
                self::INDEX => 'admin/estructura/index.html.twig',
            ],
            'routes' => [
                self::INDEX => 'admin_estructura_index',
                self::NEW => 'admin_estructura_new',
                self::EDIT => 'admin_estructura_edit',
                self::DELETE => 'admin_estructura_delete',
            ]
        ];
    }
}
