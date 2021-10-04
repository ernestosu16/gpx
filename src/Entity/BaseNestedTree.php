<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/** @Gedmo\Tree(type="nested") */
abstract class BaseNestedTree extends _Entity_
{
    protected Collection $children;

    /** @Gedmo\TreeLevel * */
    #[ORM\Column(name: 'lvl', type: 'integer')]
    protected ?int $level;

    /** @Gedmo\TreeLeft() */
    #[ORM\Column(type: 'integer')]
    protected ?int $lft;

    /** @Gedmo\TreeRight */
    #[ORM\Column(type: 'integer')]
    protected ?int $rgt;

    /**
     * @return Collection|null
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    public function setChildren(Collection $children): self
    {
        $this->children = $children;
        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;
        return $this;
    }

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(int $lft): self
    {
        $this->lft = $lft;
        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): self
    {
        $this->rgt = $rgt;
        return $this;
    }
}
