<?php

namespace App\Entity;

use App\Repository\SacaRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: SacaRepository::class)]
class Saca extends _Entity_
{

    #[ORM\Column(type: 'string', length: 13, nullable: false)]
    private string $sello;

    #[Pure]
    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getSello(): string
    {
        return $this->sello;
    }

    /**
     * @param string $sello
     */
    public function setSello(string $sello): void
    {
        $this->sello = $sello;
    }




}
