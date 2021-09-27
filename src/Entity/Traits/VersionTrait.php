<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait VersionTrait
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Version]
    protected int $version;

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;
        return $this;
    }
}
