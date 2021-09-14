<?php

namespace App\Controller\Admin;

use App\Config\Nomenclador\_Nomenclador_;
use App\Controller\_Controller_;
use JetBrains\PhpStorm\ArrayShape;

abstract class CrudController extends _Controller_
{
    const INDEX = 'index';
    const NEW = 'new';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const SHOW = 'show';

    abstract protected static function entity(): string;

    abstract protected static function formType(): string;

    abstract protected static function config(): array;

    #[ArrayShape([
        'parent' => "object",
        'translation_domain' => 'string',
        'title' => "string[]",
        'routes' => "null[]",
        'template' => "string[]",
        'page' => "array"
    ])]
    private static function default(): array
    {
        return [
            'parent' => null,
            'translation_domain' => 'nomenclador',
            'title' => [
                self::INDEX => 'Lista de nomencladores',
                self::NEW => 'Nuevo nomenclador',
                self::EDIT => 'Editar nomenclador',
                self::SHOW => 'Mostrar nomenclador',
            ],
            'template' => [
                self::INDEX => 'admin/crud/nomenclador/index.html.twig',
                self::NEW => 'admin/crud/nomenclador/new.html.twig',
                self::EDIT => 'admin/crud/nomenclador/edit.html.twig',
                self::SHOW => 'admin/crud/nomenclador/show.html.twig',
            ],
            'routes' => [self::INDEX => null, self::NEW => null, self::EDIT => null, self::SHOW => null, self::DELETE => null],
            'page' => ['limit' => 50, 'orderBy' => []]
        ];
    }

    protected function getConfig(string $key = null): mixed
    {
        $config = array_replace_recursive(self::default(), static::config());
        return $config[$key] ?? $config;
    }

    protected function getParent(): _Nomenclador_
    {
        return $this->getConfig('parent');
    }

    protected function getCode(): string
    {
        return $this->getParent()->getCode();
    }

    protected function getTitle(string $key): ?string
    {
        $titles = $this->getConfig('title');
        return $titles[$key] ?? null;
    }

    protected function getTemplate(string $key): ?string
    {
        $templetes = $this->getConfig('template');
        return $templetes[$key] ?? null;
    }

    protected function getRoute(string $key): ?string
    {
        $routes = $this->getConfig('routes');
        return $routes[$key] ?? null;
    }
}
