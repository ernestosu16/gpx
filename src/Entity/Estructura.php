<?php

namespace App\Entity;

use App\Util\RegexUtil;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/** @Gedmo\Tree(type="nested") */
#[ORM\Entity]
#[ORM\Index(columns: ['parent_id'], name: 'IDX_PARENT_ID')]
#[ORM\Index(columns: ['root_id'], name: 'IDX_ROOT_ID')]
#[ORM\Index(columns: ['discr'], name: 'IDX_DISCR')]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string', length: 17)]
#[ORM\DiscriminatorMap(value: [
    "cliente" => Empresa::class,
])]
class Estructura extends BaseNestedTree
{
    /** @Gedmo\TreeRoot() */
    #[ORM\ManyToOne(targetEntity: Estructura::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Estructura $root;

    /** @Gedmo\TreeParent() */
    #[ORM\ManyToOne(targetEntity: Estructura::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Estructura $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Estructura::class, cascade: ['persist'])]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    #[MaxDepth(1)]
    protected Collection $children;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: RegexUtil::CODIGO)]
    private string $codigo;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: RegexUtil::TEXTO_ACENTO_ESPACIO)]
    #[Assert\Length(min: 3, max: 100)]
    private string $nombre;

    #[ORM\Column(type: 'text', length: 500)]
    #[Assert\Length(max: 500)]
    private string $descripcion = '';

    #[ORM\Column(type: 'text', length: 5)]
    #[Assert\Length(min: 5, max: 5)]
    #[Assert\Regex(pattern: RegexUtil::SOLO_NUMERO)]
    private ?string $codigo_postal;

    #[ORM\Column(type: 'json')]
    private array $parametros = array();

}
