<?php

namespace App\Repository;

use App\Entity\Menu;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MenuRepository extends _NestedTreeRepository_
{
    protected static function classEntity(): string
    {
        return Menu::class;
    }

    public function buildTreeHierarchyEntity(array $nodes): ?Menu
    {
        /** @var Menu $tree */
        $tree = $this->childrenHierarchyEntity();
        if (!$tree)
            return null;

        $listNodes = [];
        foreach ($nodes as $item) {
            $listNodes = array_merge($listNodes, $this->getPath($item));
        }

        /** @var Menu $menus */
        $menus = $this->buildTreeEntity(array_unique($listNodes));
        return $menus;
    }
}
