<?php

namespace App\Entity;

use App\Repository\NomencladorRepository;
use App\Util\RegexUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/** @Gedmo\Tree(type="nested") */
#[ORM\Entity(repositoryClass: NomencladorRepository::class)]
#[ORM\Index(columns: ['parent_id'], name: 'IDX_PARENT_ID')]
#[ORM\Index(columns: ['root_id'], name: 'IDX_ROOT_ID')]
#[ORM\Index(columns: ['discr'], name: 'IDX_DISCR')]
#[ORM\UniqueConstraint(name: 'UNQ_CODIGO', fields: ['codigo'])]
#[UniqueEntity(fields: ['codigo'])]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string', length: 11)]
#[ORM\DiscriminatorMap(value: ["nomenclador" => Nomenclador::class, "grupo" => Grupo::class, "menu" => Menu::class])]
class Nomenclador extends BaseNestedTree
{
    const ROOT = 'APP';

    /** @Gedmo\TreeRoot() */
    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Nomenclador $root;

    /** @Gedmo\TreeParent() */
    #[ORM\ManyToOne(targetEntity: Nomenclador::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Nomenclador $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    #[MaxDepth(1)]
    private ?Collection $children;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: RegexUtil::CODIGO, message: 'regex.codigo')]
    private string $codigo;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: RegexUtil::TEXTO_ACENTO_ESPACIO, message: 'regex.nombre')]
    #[Assert\Length(min: 3, max: 100)]
    private string $nombre;

    #[ORM\Column(type: 'text', length: 500)]
    #[Assert\Length(max: 500)]
    private string $descripcion = '';

    #[ORM\Column(type: 'json')]
    private array $parametros = array();

    #[ORM\Column(type: 'boolean')]
    private bool $end = false;

    #[ORM\Column(type: 'boolean')]
    private bool $habilitado = true;

    public function __toString(): string
    {
        return $this->nombre;
    }

    #[Pure]
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getRoot(): ?self
    {
        return $this->root;
    }

    public function setRoot(?self $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Nomenclador[]
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    public function addChild(Nomenclador $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Nomenclador $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = mb_strtoupper($codigo);

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = ucfirst($nombre);

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getParametros(): ?array
    {
        return $this->parametros;
    }

    public function setParametros(array $parametros): self
    {
        $this->parametros = $parametros;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnd(): bool
    {
        return $this->end;
    }

    /**
     * @param bool $end
     * @return Nomenclador
     */
    public function setEnd(bool $end): Nomenclador
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHabilitado(): bool
    {
        return $this->habilitado;
    }

    public function setHabilitado(bool $habilitado): self
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    public function getHabilitado(): ?bool
    {
        return $this->habilitado;
    }
}
