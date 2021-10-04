<?php

namespace App\Form\Admin;

use App\Entity\Estructura;
use App\Entity\TrabajadorCredencial;
use App\Repository\EstructuraRepository;
use App\Repository\GrupoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class BaseAdminType extends AbstractType
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TokenStorageInterface  $tokenStorage,
        protected GrupoRepository        $grupoRepository,
        protected EstructuraRepository   $estructuraRepository
    )
    {
    }

    /**
     * @return Estructura[]
     */
    protected function getChoiceEstructuras(): array
    {
        if (in_array('ROLE_ADMIN', $this->tokenStorage->getToken()->getUser()->getRoles()))
            return $this->estructuraRepository->findAll();

        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->tokenStorage->getToken()->getUser();

        # listado de estructura
        return array_unique(array_merge(
            [$credencial->getEstructura()],
            $this->estructuraRepository->children($credencial->getEstructura())
        ));
    }

    protected function getChoiceGrupos(): array
    {
        if (in_array('ROLE_ADMIN', $this->tokenStorage->getToken()->getUser()->getRoles()))
            return $this->grupoRepository->findAll();

        $estructuras = $this->getChoiceEstructuras();

        $grupos = [];
        foreach ($estructuras as $estructura) {
            foreach ($estructura->getTipos() as $tipo) {
                $grupos = array_merge($grupos, $tipo->getGrupos()->toArray());
            }
            $grupos = array_merge($grupos, $estructura->getGrupos()->toArray());
        }

        return array_unique($grupos);
    }
}
