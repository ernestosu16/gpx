<?php

namespace App\Controller\Admin;

use App\Entity\Grupo;
use App\Form\Admin\GrupoType;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/grupo', name: 'admin_grupo')]
final class GrupoController extends _CrudController_
{
    protected static function entity(): string
    {
        return Grupo::class;
    }

    protected static function formType(): string
    {
        return GrupoType::class;
    }

    protected static function config(): array
    {
        return [
            'titles' => [
                self::INDEX => 'Listado de los grupos',
            ],
            'templates' => [
                self::INDEX => 'admin/grupo/index.html.twig',
                self::NEW => 'admin/grupo/new.html.twig',
                self::EDIT => 'admin/grupo/edit.html.twig',
            ],
            'routes' => [
                self::INDEX => 'admin_grupo_index',
                self::NEW => 'admin_grupo_new',
                self::EDIT => 'admin_grupo_edit',
                self::SHOW => 'admin_grupo_show',
                self::DELETE => 'admin_grupo_delete',
            ],
        ];
    }
}
