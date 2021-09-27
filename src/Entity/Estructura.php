<?php

namespace App\Entity;

use App\Entity\Traits\VersionTrait;
use App\Repository\EstructuraRepository;
use App\Utils\RegexUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMException;
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
    use VersionTrait;

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

    #[ORM\ManyToMany(targetEntity: Localizacion::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'estructura_localizacion_asignada')]
    private Collection $localizaciones;

    #[ORM\ManyToMany(targetEntity: EstructuraTipo::class, inversedBy: 'estructuras')]
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
        $this->localizaciones = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nombre;
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
     * @return Collection|Estructura[]
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
     * @return Collection|Localizacion[]
     */
    public function getLocalizaciones(): Collection
    {
        return $this->localizaciones;
    }

    public function addLocalizacion(Localizacion $localizacion): self
    {
        if (!$this->localizaciones->contains($localizacion)) {
            $this->localizaciones[] = $localizacion;
        }

        return $this;
    }

    public function removeLocalizacion(Localizacion $localizacion): self
    {
        $this->localizaciones->removeElement($localizacion);

        return $this;
    }

    /**
     * @return Collection|EstructuraTipo[]
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

    #[Pure] public function getMunicipio(): ?Localizacion
    {

        foreach ($this->getLocalizaciones() as $localizacion) {
            if ($localizacion->getTipo() && $localizacion->getTipo()->getCodigo() == LocalizacionTipo::MUNICIPIO)
                return $localizacion;
        }
        return null;
    }

    /**
     * @throws ORMException
     */
    public function setMunicipio(Localizacion $municipio): self
    {
        foreach ($this->getLocalizaciones() as $localizacion) {
            if ($localizacion->getTipo()->getCodigo() === LocalizacionTipo::MUNICIPIO)
                $this->removeLocalizacion($localizacion);
        }

        if ($municipio->getCodigo() === LocalizacionTipo::MUNICIPIO)
            throw new ORMException('La localización no es de tipo Municipio');

        $this->addLocalizacion($municipio);
        return $this;
    }
}
