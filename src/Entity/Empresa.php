<?php

namespace App\Entity;

use App\Util\RegexUtil;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Empresa extends Estructura
{
    #[ORM\Column(type: "string", length: 11, unique: true)]
    #[Assert\NotNull]
    #[Assert\Regex(pattern: RegexUtil::CODIGO_REEUP)]
    private ?string $codigo_reeup;

    #[ORM\Column(type: "string", length: 16)]
    #[Assert\NotNull]
    #[Assert\Length(min: 16, max: 16)]
    #[Assert\Regex(pattern: RegexUtil::CODIGO_NIT)]
    private ?string $codigo_nit;
}
