<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/** @Gedmo\Tree(type="nested") */
abstract class BaseNestedTree extends _Entity_
{
    /** @Gedmo\TreeLeft() */
    #[ORM\Column(type: 'integer')]
    protected ?int $lft;

    /** @Gedmo\TreeRight */
    #[ORM\Column(type: 'integer')]
    protected ?int $rgt;

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
