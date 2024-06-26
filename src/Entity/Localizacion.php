<?php

namespace App\Entity;

use App\Entity\Nomenclador\LocalizacionTipo;
use App\Entity\Traits\VersionTrait;
use App\Repository\LocalizacionRepository;
use App\Utils\RegexUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\String\u;

/** @Gedmo\Tree(type="nested") */
#[ORM\Table(name: 'app_localizacion')]
#[ORM\Entity(repositoryClass: LocalizacionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_CODIGO', columns: ['codigo'])]
#[ORM\Index(columns: ['root_id'], name: 'IDX_ROOT_ID')]
#[ORM\Index(columns: ['parent_id'], name: 'IDX_PARENT_ID')]
#[ORM\Index(columns: ['localizacion_tipo_id'], name: 'IDX_LOCALIZACION_TIPO_ID')]
class Localizacion extends BaseNestedTree
{
    use VersionTrait;

    /** @Gedmo\TreeRoot() */
    #[ORM\ManyToOne(targetEntity: Localizacion::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Localizacion $root;

    /** @Gedmo\TreeParent() */
    #[ORM\ManyToOne(targetEntity: Localizacion::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected ?Localizacion $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Localizacion::class, cascade: ['persist'])]
    #[ORM\OrderBy(['lft' => 'ASC'])]
    #[MaxDepth(1)]
    protected Collection $children;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: RegexUtil::CODIGO, message: 'regex.codigo')]
    #[Groups(['default'])]
    protected string $codigo;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: RegexUtil::TEXTO_ACENTO_ESPACIO, message: 'regex.nombre')]
    #[Assert\Length(min: 3, max: 100)]
    #[Groups(['default'])]
    protected string $nombre;

    #[ORM\Column(type: 'text', length: 500)]
    #[Assert\Length(max: 500)]
    protected string $descripcion = '';

    #[ORM\Column(type: 'text', length: 2)]
    #[Assert\Length(max: 2)]
    #[Groups(['default'])]
    protected string $codigo_aduana = '';

    #[ORM\ManyToOne(targetEntity: LocalizacionTipo::class)]
    #[ORM\JoinColumn(name: 'localizacion_tipo_id', nullable: false)]
    #[Groups(['default'])]
    protected ?LocalizacionTipo $tipo;

    #[Pure] public function __construct()
    {
        $this->children = new ArrayCollection();
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
        $this->codigo =  u($codigo)->replace(' ', '_')->upper();

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
     * @return Collection|Localizacion[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Localizacion $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Localizacion $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getTipo(): ?LocalizacionTipo
    {
        return $this->tipo;
    }

    public function setTipo(?LocalizacionTipo $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getCodigoAduana(): string
    {
        return $this->codigo_aduana;
    }

    public function setCodigoAduana(string $codigo_aduana): Localizacion
    {
        $this->codigo_aduana = $codigo_aduana;
        return $this;
    }

    public function getNombreConTipo()
    {
        return sprintf('%s (%s)', $this->nombre, $this->tipo->getNombre());
    }
}
