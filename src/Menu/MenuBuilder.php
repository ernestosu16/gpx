<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;

final class MenuBuilder extends _Menu_
{
    use ContainerAwareTrait;

    public function __construct(private FactoryInterface $factory)
    {

    }

    public function createMainMenu(RequestStack $requestStack): ItemInterface
    {
        $menu = $this->factory->createItem('root', array(
            'currentClass' => 'active',
            'childrenAttributes' => array(
                'class' => 'nav',
                'id' => 'side-menu'
            ),
        ));

        $menu->addChild('Dashboard', ['route' => 'dashboard']);

        $menu->addChild('administrator', array(
            'label' => '<span class="nav-label">Administración</span><span class="fa arrow"></span>',
            'uri' => '#',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
            'attributes' => ['aria-expanded' => 'true', 'class' => 'active'],
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse', 'aria-expanded' => true]
        ));
        $menu['administrator']->addChild('Trabajadores', [
            'route' => 'admin_trabajador_index',
            'label' => '<span class="fa fa-user"></span>  Trabajadores',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);
        $menu['administrator']->addChild('Grupos', [
            'route' => 'admin_grupo_index',
            'label' => '<span class="fa fa-group"></span> Grupos',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);
        $menu['administrator']->addChild('Menu', [
            'route' => 'admin_menu_index',
            'label' => '<span class="fa fa-list"></span> Menu',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);
        $menu['administrator']->addChild('Nomencladores', [
            'route' => 'admin_nomenclador_index',
            'label' => '<span class="fa fa-wrench"></span> Nomenclador',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);

        return $menu;
    }
}
