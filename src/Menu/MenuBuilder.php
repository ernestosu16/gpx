<?php

namespace App\Menu;

use App\Config\Data\Nomenclador\MenuData;
use App\Entity\Menu;
use Exception;
use Knp\Menu\ItemInterface;

final class MenuBuilder extends _Menu_
{
    /**
     * @throws Exception
     */
    public function createMainMenu(): ItemInterface
    {
        $factory = $this->factory;
        $root = $this->getRoot();

        if (!$root || !$root->getChildren()->count())
            throw new Exception('Ejecute el comando "bin/console app:configurar:fixture"');

        $item = $factory->createItem($root->getCodigo(), [
            'currentClass' => 'active',
            'childrenAttributes' => [
                'class' => 'nav',
                'id' => 'side-menu'
            ],
        ]);

        $item->addChild('Dashboard', [
            'route' => 'dashboard',
            'extras' => ['translation_domain' => false]
        ]);
        foreach ($root->getChildren() as $child) {
            $item = $this->createItem($item, $child);
        }

        return $item;
    }

    private function getRoot(): ?Menu
    {
        return $this->entityManager->getRepository(Menu::class)->findOneByCodigo(MenuData::code());
    }

    /**
     * @throws Exception
     */
    private function createItem(ItemInterface $item, ?Menu $menu): ItemInterface
    {
        if (!$menu || !$menu->getHabilitado())
            return $item;

        $icon = ($menu->getIcon()) ? sprintf('<span class="%s"></span>', $menu->getIcon()) : '<span class="fa fa-list"></span>';
        $notify = ($menu->checkNotify()) ? '</span><span class="label label-success pull-right">N</span>' : '';

        if ($menu->getChildren()->count()) {

            $label = sprintf('<span class="nav-label" title="%s">%s</span><span class="fa arrow"></span> %s',
                $menu->getDescripcion(), $menu->getNombre(), $notify);
            $root = $item->addChild($menu->getCodigo(), array(
                'label' => $label,
                'uri' => '#',
                'extras' => ['safe_label' => true, 'translation_domain' => false],
                'attributes' => ['aria-expanded' => 'true'/*, 'class' => 'active'*/],
                'childrenAttributes' => ['class' => 'nav nav-second-level collapse', 'aria-expanded' => true]
            ));

            foreach ($menu->getChildren() as $child) {
                $this->createItem($root, $child);
            }
        } else {

            if ($this->requestStack->getCurrentRequest()->get('_route') === $menu->getRoute()) {
                $this->itemClassActive($item);
            }

            $label = sprintf('%s <span class="nav-label" title="%s">%s</span> %s',
                $icon, $menu->getDescripcion(), $menu->getNombre(), $notify);

            $item->addChild($menu->getCodigo(), [
                'route' => $menu->getRoute() ?? null,
                'label' => $label,
                'extras' => ['safe_label' => true, 'translation_domain' => false],
                'attributes' => ['class' => $this->requestStack->getCurrentRequest()->get('_route') === $menu->getRoute() ? 'active' : ''],
            ]);
        }

        return $item;
    }

    private function itemClassActive(ItemInterface $item)
    {
        if ($parent = $item->getParent()) {
            $item->setAttribute('class', 'active');
            $this->itemClassActive($parent);
        }

        $item->setAttribute('class', 'active');
    }
}
