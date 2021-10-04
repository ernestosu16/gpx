<?php

namespace App\Entity;

use App\Entity\Traits\VersionTrait;
use App\Repository\NomencladorRepository;
use App\Utils\RegexUtil;
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
#[ORM\Cache]
#[UniqueEntity(fields: ['codigo'])]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string', length: 17)]
#[ORM\DiscriminatorMap(value: [
    "nomenclador" => Nomenclador::class,
    "grupo" => Grupo::class,
    "menu" => Menu::class,
    "estructura_tipo" => EstructuraTipo::class,
    "localizacion_tipo" => LocalizacionTipo::class,
    "agencia" => Agencia::class,
])]
class Nomenclador extends BaseNestedTree
{
    use VersionTrait;

    /** @Gedmo\TreeRoot() */
    #[ORM\ManyToOne(targetEntity: Nomenclador::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Nomenclador $root = null;

    /** @Gedmo\TreeParent() */
    #[ORM\ManyToOne(targetEntity: Nomenclador::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Nomenclador $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Nomenclador::class, cascade: ['persist'])]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    #[MaxDepth(1)]
    protected Collection $children;

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
    public function getChildren(): Collection
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

    public function hasParametro(string $key): bool
    {
        return isset($this->parametros[$key]);
    }


    public function getParametro(string $key): string|array|null
    {
        return $this->parametros[$key] ?? null;
    }

    public function setParametro(string $key, null|string|array $value): static
    {
        $this->parametros[$key] = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnd(): bool
    {
        return $this->end;
    }

    public function setEnd(bool $end): self
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

    public function getEnd(): ?bool
    {
        return $this->end;
    }
}
