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
}
