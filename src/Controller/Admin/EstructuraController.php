<?php

namespace App\Controller\Admin;

use App\Entity\Estructura;
use App\Entity\TrabajadorCredencial;
use App\Form\Admin\EstructuraType;
use App\Repository\EstructuraRepository;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/estructura', name: 'admin_estructura')]
final class EstructuraController extends _CrudController_
{
    #[Pure] public function __construct(
        private EstructuraRepository $estructuraRepository,
        protected PaginatorInterface $paginator
    )
    {
        parent::__construct($paginator);
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
                self::INDEX => 'admin/estructura/index.html.twig',
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

        $pagination = $this->paginator->paginate(
            $this->getEstructuras(),
            $request->query->getInt('page', 1),
            $settings['page']['limit']
        );

        return $this->render($settings['templates'][self::INDEX], [
            'settings' => $settings,
            'pagination' => $pagination,
        ]);
    }

    private function getEstructuras(): QueryBuilder
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        $query = $this->estructuraRepository->createQueryBuilder('e');

        if (in_array('ROLE_ADMIN', $credencial->getRoles()))
            return $query;

        # Obtengo la lista de estructura subordinadas y principal del trabajador
        $query->andWhere('e.parent = :parent ')->setParameter('parent', $credencial->getTrabajador()->getEstructura())
            ->orWhere('e = :estructura ')->setParameter('estructura', $credencial->getTrabajador()->getEstructura());

        return $query;
    }
}
