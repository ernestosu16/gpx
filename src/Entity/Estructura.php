<?php

namespace App\Entity;

use App\Repository\EstructuraRepository;
use App\Util\RegexUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/** @Gedmo\Tree(type="nested") */
#[ORM\Entity(repositoryClass: EstructuraRepository::class)]
#[ORM\Index(columns: ['parent_id'], name: 'IDX_PARENT_ID')]
#[ORM\Index(columns: ['root_id'], name: 'IDX_ROOT_ID')]
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

    #[ORM\ManyToMany(targetEntity: EstructuraTipo::class)]
    #[ORM\JoinTable(name: 'estructura_tipo_asignado')]
    private Collection $tipos;

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

    #[ORM\Column(type: 'boolean')]
    private bool $habilitado = true;

    #[ORM\Column(type: 'json')]
    private array $parametros = array();

    #[Pure] public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->tipos = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nombre;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

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

    public function getCodigoPostal(): ?string
    {
        return $this->codigo_postal;
    }

    public function setCodigoPostal(string $codigo_postal): self
    {
        $this->codigo_postal = $codigo_postal;

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
     * @return Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Estructura $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Estructura $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTipos(): Collection
    {
        return $this->tipos;
    }

    public function addTipo(EstructuraTipo $tipo): self
    {
        if (!$this->tipos->contains($tipo)) {
            $this->tipos[] = $tipo;
        }

        return $this;
    }

    public function removeTipo(EstructuraTipo $tipo): self
    {
        $this->tipos->removeElement($tipo);

        return $this;
    }

    public function isHabilitado(): bool
    {
        return $this->habilitado;
    }

    public function setHabilitado(bool $habilitado): Estructura
    {
        $this->habilitado = $habilitado;
        return $this;
    }
}
