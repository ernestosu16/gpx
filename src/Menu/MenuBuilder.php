<?php

namespace App\Menu;

use App\Config\Nomenclador\Menu as MenuNomenclador;
use App\Entity\Menu;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class MenuBuilder extends _Menu_
{
    public function createMainMenu(RequestStack $requestStack): ItemInterface
    {
        $factory = $this->getFactory();
        $root = $this->getRoot();

        if (!$root || !$root->getChildren()->count())
            return $this->default($factory);

        $item = $factory->createItem($root->getCodigo(), [
            'currentClass' => 'active',
            'childrenAttributes' => [
                'class' => 'nav',
                'id' => 'side-menu'
            ],
        ]);

        $item->addChild('Dashboard', ['route' => 'dashboard']);
        foreach ($root->getChildren() as $child) {
            $item = $this->createItem($item, $child);
        }

        return $item;
    }

    private function getRoot(): ?Menu
    {
        return $this->getEntityManager()->getRepository(Menu::class)->findOneByCodigo(MenuNomenclador::code());
    }

    private function createItem(ItemInterface $item, ?Menu $menu): ItemInterface
    {
        if (!$menu)
            return $item;

        if ($menu->getChildren()->count()) {

            $root = $item->addChild($menu->getCodigo(), array(
                'label' => sprintf('<span class="nav-label">%s</span><span class="fa arrow"></span>', $menu->getNombre()),
                'uri' => '#',
                'extras' => ['safe_label' => true, 'translation_domain' => false],
                'attributes' => ['aria-expanded' => 'true'/*, 'class' => 'active'*/],
                'childrenAttributes' => ['class' => 'nav nav-second-level collapse', 'aria-expanded' => true]
            ));

            foreach ($menu->getChildren() as $child) {
                $this->createItem($root, $child);
            }
        } else {
            /** @var RequestStack $requestStack */
            $requestStack = $this->get('request_stack');
            if ($requestStack->getCurrentRequest()->get('_route') === $menu->getRoute()) {
                $item->setAttribute('class', 'active');
            }


            $icon = ($menu->getIcon()) ? sprintf('<span class="%s"></span>', $menu->getIcon()) : '';
            $notify = ($menu->getNotify()) ? '</span><span class="label label-success pull-right">N</span>' : '';
            $label = sprintf(
                '%s <span class="nav-label" title="%s">%s</span> %s',
                $icon,
                $menu->getDescripcion(),
                $menu->getNombre(),
                $notify
            );
            $item->addChild($menu->getCodigo(), [
                'route' => $menu->getRoute() ?? null,
                'label' => $label,
                'extras' => ['safe_label' => true, 'translation_domain' => false],
                'attributes' => ['class' => $requestStack->getCurrentRequest()->get('_route') === $menu->getRoute() ? 'active' : ''],
            ]);
        }

        return $item;
    }

    private function default(FactoryInterface $factory): ItemInterface
    {
        $item = $factory->createItem('root', array(
            'currentClass' => 'active',
            'childrenAttributes' => array(
                'class' => 'nav',
                'id' => 'side-menu'
            ),
        ));

        $item->addChild('Dashboard', ['route' => 'dashboard']);

        $item->addChild('administrator', array(
            'label' => '<span class="nav-label">Administración</span><span class="fa arrow"></span>',
            'uri' => '#',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
            'attributes' => ['aria-expanded' => 'true', 'class' => 'active'],
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse', 'aria-expanded' => true]
        ));
        $item['administrator']->addChild('Trabajadores', [
            'route' => 'admin_trabajador_index',
            'label' => '<span class="fa fa-user"></span>  Trabajadores',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);
        $item['administrator']->addChild('Grupos', [
            'route' => 'admin_grupo_index',
            'label' => '<span class="fa fa-group"></span> Grupos',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);
        $item['administrator']->addChild('Menu', [
            'route' => 'admin_menu_index',
            'label' => '<span class="fa fa-list"></span> Menú',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);
        $item['administrator']->addChild('Nomencladores', [
            'route' => 'admin_nomenclador_index',
            'label' => '<span class="fa fa-wrench"></span> Nomenclador',
            'extras' => ['safe_label' => true, 'translation_domain' => false],
        ]);

        return $item;
    }
}
