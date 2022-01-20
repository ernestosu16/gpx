<?php

namespace App\Controller\Admin;

use App\Entity\Estructura;
use App\Entity\TrabajadorCredencial;
use App\Form\Admin\EstructuraType;
use App\Repository\EstructuraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Pure;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/estructura', name: 'admin_estructura')]
final class EstructuraController extends _CrudController_
{
    #[Pure] public function __construct(
        protected ManagerRegistry    $managerRegistry,
        protected PaginatorInterface $paginator,
        private EstructuraRepository $estructuraRepository,
    )
    {
        parent::__construct($managerRegistry, $paginator);
    }

    protected static function entity(): string
    {
        return Estructura::class;
    }

    protected static function formType(): string
    {
        return EstructuraType::class;
    }

    protected static function config(): array
    {
        return [
            'titles' => [
                self::INDEX => 'Listado de las estructuras',
                self::NEW => 'Nueva estructura',
                self::EDIT => 'Editar estructura',
            ],
            'templates' => [
                self::INDEX => 'admin/estructura/index_nestable.html.twig',
                self::NEW => 'admin/estructura/form.html.twig',
                self::EDIT => 'admin/estructura/form.html.twig',
            ],
            'routes' => [
                self::INDEX => 'admin_estructura_index',
                self::NEW => 'admin_estructura_new',
                self::EDIT => 'admin_estructura_edit',
                self::DELETE => 'admin_estructura_delete',
            ]
        ];
    }

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted([], $request);

        $settings = $this->settings();
        return $this->render($settings['templates'][self::INDEX], [
            'settings' => $settings,
            'nodes' => $this->getRootNodes(),
        ]);
    }

    private function getRootNodes()
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        if (in_array('ROLE_ADMIN', $credencial->getRoles()))
            return $this->estructuraRepository->getRootNodes('nombre');

        return new ArrayCollection([$credencial->getEstructura()]);
    }
}
