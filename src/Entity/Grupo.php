<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: GrupoRepository::class)]
class Grupo extends Nomenclador
{
    #[ORM\ManyToMany(targetEntity: Menu::class)]
    #[ORM\JoinTable(name: 'grupo_menu_asignado')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    private Collection $menus;

    #[ORM\ManyToMany(targetEntity: Estructura::class, mappedBy: 'grupos', cascade: ['persist'])]
    private Collection $estructuras;

    #[ORM\ManyToMany(targetEntity: EstructuraTipo::class, mappedBy: 'grupos', cascade: ['persist'])]
    private Collection $estructura_tipos;

    #[Pure] public function __construct()
    {
        parent::__construct();
        $this->menus = new ArrayCollection();
        $this->estructuras = new ArrayCollection();
        $this->estructura_tipos = new ArrayCollection();
    }

    /**
     * @return Collection|Menu[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        $this->menus->removeElement($menu);

        return $this;
    }

    /**
     * @return Collection|Estructura[]
     */
    public function getEstructuras(): Collection
    {
        return $this->estructuras;
    }

    public function addEstructura(Estructura $estructura): self
    {
        if (!$this->estructuras->contains($estructura)) {
            $this->estructuras[] = $estructura;
            $estructura->addGrupo($this);
        }

        return $this;
    }

    public function removeEstructura(Estructura $estructura): self
    {
        if ($this->estructuras->removeElement($estructura)) {
            $estructura->removeGrupo($this);
        }

        return $this;
    }

    public function getAccesos(): array
    {
        return $this->hasParametro('accesos') ? $this?->getParametro('accesos') : [];
    }

    public function setAccesos(array $routes): self
    {
        $this->setParametro('accesos', $routes);

        return $this;
    }

    /**
     * @return Collection|EstructuraTipo[]
     */
    public function getEstructuraTipos(): Collection
    {
        return $this->estructura_tipos;
    }

    public function addEstructuraTipo(EstructuraTipo $estructuraTipo): self
    {
        if (!$this->estructura_tipos->contains($estructuraTipo)) {
            $this->estructura_tipos[] = $estructuraTipo;
            $estructuraTipo->addGrupo($this);
        }

        return $this;
    }

    public function removeEstructuraTipo(EstructuraTipo $estructuraTipo): self
    {
        if ($this->estructura_tipos->removeElement($estructuraTipo)) {
            $estructuraTipo->removeGrupo($this);
        }

        return $this;
    }
}
