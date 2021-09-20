<?php

namespace App\Controller\Admin;

use App\Config\Data\Nomenclador\GrupoData;
use App\Entity\Grupo;
use App\Form\Admin\GrupoType;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/grupo', name: 'admin_grupo')]
final class GrupoController extends CrudNomencladorController
{
    protected static function entity(): string
    {
        return Grupo::class;
    }

    protected static function formType(): string
    {
        return GrupoType::class;
    }

    #[ArrayShape(['parent' => "\App\Config\Nomenclador\Grupo", 'title' => "string[]", 'routes' => "string[]"])]
    protected static function config(): array
    {
        return [
            'parent' => GrupoData::newInstance(),
            'title' => [
                self::INDEX => 'Lista de grupos',
                self::NEW => 'Nuevo grupo',
                self::EDIT => 'Editar grupo',
                self::SHOW => 'Mostrar grupo',
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
