<?php

namespace App\Form\Admin;

use App\Entity\Estructura;
use App\Entity\TrabajadorCredencial;
use App\Repository\EstructuraRepository;
use App\Repository\Nomenclador\GrupoRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use function Symfony\Component\String\u;

abstract class BaseAdminType extends AbstractType
{
    public function __construct(
        protected ContainerInterface       $container,
        protected EntityManagerInterface   $entityManager,
        protected TokenStorageInterface    $tokenStorage,
        protected GrupoRepository          $grupoRepository,
        protected EstructuraRepository     $estructuraRepository,
        protected EventDispatcherInterface $dispatcher
    )
    {
    }

    /**
     * @return Estructura[]
     */
    protected function getChoiceEstructuras(array $incluir = [], array $excluir = []): array
    {
        if (in_array('ROLE_ADMIN', $this->tokenStorage->getToken()->getUser()->getRoles()))
            return $this->estructuraRepository->findAll();

        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->tokenStorage->getToken()->getUser();

        # listado de estructura
        $collection = array_unique(array_merge([$credencial->getEstructura()],
            $this->estructuraRepository->children($credencial->getEstructura()),
            $incluir
        ));

        # Quitando de la lista
        foreach ($excluir as $item) {
            $collection = array_filter($collection, function (Estructura $estructura) use ($item) {
                return $estructura !== $item ? $estructura : null;
            });
        }

        return $collection;
    }

    protected function getChoiceGrupos(): array
    {
        if (in_array('ROLE_ADMIN', $this->tokenStorage->getToken()->getUser()->getRoles()))
            return $this->grupoRepository->findAll();

        $estructuras = $this->getChoiceEstructuras();

        $grupos = [];
        foreach ($estructuras as $estructura) {
            foreach ($estructura->getTipos() as $tipo) {
                if ($tipo->getChildren()->count() > 0) {
                    $grupos = $this->getTiposChildrenGrupos($grupos, $tipo->getChildren());
                } else {
                    $grupos = array_merge($grupos, $tipo->getGrupos()->toArray());
                }
            }
            $grupos = array_merge($grupos, $estructura->getGrupos()->toArray());
        }
        usort($grupos, $this->objectSorter('nombre', 'ASC'));
        return array_unique($grupos);
    }

    private function getTiposChildrenGrupos(mixed $grupos, Collection $tipos)
    {
        foreach ($tipos as $tipo) {
            if ($tipo->getChildren()->count() > 0) {
                $grupos = $this->getTiposChildrenGrupos($grupos, $tipo->getChildren());
            } else {
                $grupos = array_merge($grupos, $tipo->getGrupos()->toArray());
            }
        }

        return $grupos;
    }

    function objectSorter(string $clave, $orden = null): \Closure
    {
        return function ($a, $b) use ($clave, $orden) {
            $getter = (string)u($clave)->title()->ensureStart('get');
            return ($orden == "DESC") ? strnatcmp($b->$getter(), $a->$getter()) : strnatcmp($a->$getter(), $b->$getter());
        };
    }
}
