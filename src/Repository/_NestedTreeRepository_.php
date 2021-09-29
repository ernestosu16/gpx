<?php

namespace App\Repository;

use App\Entity\BaseNestedTree;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

abstract class _NestedTreeRepository_ extends NestedTreeRepository
{
    protected abstract static function classEntity(): string;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(static::classEntity()));
    }

    public function findOneByCodigo(?string $code): ?object
    {
        return $code ? $this->findOneBy(['codigo' => $code]) : null;
    }

    public function findOneByCodigoHabilitado(string $code): ?object
    {
        return $this->findOneBy(['codigo' => $code, 'habilitado' => true]);
    }

    public function buildTreeEntity(array $nodes): ?BaseNestedTree
    {
        $nestedTree = null;
        if (count($nodes) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = [];
            foreach ($nodes as $child) {
                /** @var BaseNestedTree $item */
                $item = $child;
                $item->setChildren(new ArrayCollection());
                $l = count($stack);
                // Check if we're dealing with different levels
                while ($l > 0 && $stack[$l - 1]->getLevel() >= $item->getLevel()) {
                    array_pop($stack);
                    --$l;
                }
                // Stack is empty (we are inspecting the root)
                if (0 == $l) {
                    $nestedTree = $item;
                    $stack[] = $item;
                } else {
                    // Add child to parent
                    $i = count($stack[$l - 1]->getChildren());
                    $stack[$l - 1]->getChildren()[$i] = $item;
                    $stack[] = &$stack[$l - 1]->getChildren()[$i];
                }
            }
        }
        return $nestedTree;
    }

    public function childrenHierarchyEntity(): ?BaseNestedTree
    {
        $qb = $this->getNodesHierarchyQueryBuilder();
        $nodes = $qb->getQuery()->getResult();
        return $this->buildTreeEntity($nodes);
    }
}
