<?php

namespace App\Controller\Admin;

use App\Config\Data\_Data_;
use App\Controller\_Controller_;
use JetBrains\PhpStorm\ArrayShape;

abstract class CrudController extends _Controller_
{
    const INDEX = 'index';
    const NEW = 'new';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const SHOW = 'show';

    private array $titles = [
        self::INDEX => 'Lista de nomencladores',
        self::NEW => 'Nuevo nomenclador',
        self::EDIT => 'Editar nomenclador',
        self::SHOW => 'Mostrar nomenclador',
    ];

    protected array $template = [];

    private array $routes = [self::INDEX => null, self::NEW => null, self::EDIT => null, self::SHOW => null, self::DELETE => null];

    private array $page = ['limit' => 50, 'orderBy' => []];

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
    private function default(): array
    {
        return [
            'parent' => null,
            'translation_domain' => 'nomenclador',
            'title' => $this->titles,
            'template' => $this->template,
            'routes' => $this->routes,
            'page' => $this->page,
        ];
    }

    protected function getConfig(string $key = null): mixed
    {
        $config = array_replace_recursive(self::default(), static::config());
        return $config[$key] ?? $config;
    }

    protected function getParent(): _Data_
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
