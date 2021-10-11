<?php

namespace App\Controller\Admin;

use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use App\Form\Admin\TrabajadorType;
use App\Repository\EstructuraRepository;
use App\Repository\TrabajadorRepository;
use JetBrains\PhpStorm\Pure;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/trabajador', name: 'admin_trabajador')]
final class TrabajadorController extends _CrudController_
{
    #[Pure] public function __construct(
        private TrabajadorRepository $trabajadorRepository,
        private EstructuraRepository $estructuraRepository,
        protected PaginatorInterface $paginator
    )
    {
        parent::__construct($paginator);
    }

    protected static function entity(): string
    {
        return Trabajador::class;
    }

    protected static function formType(): string
    {
        return TrabajadorType::class;
    }

    protected static function config(): array
    {
        return [
            'titles' => [
                self::INDEX => 'Listado de los trabajadores',
                self::NEW => 'Nuevo trabajador',
                self::EDIT => 'Editar trabajador',
            ],
            'templates' => [
                self::INDEX => 'admin/trabajador/index.html.twig',
                self::NEW => 'admin/trabajador/new.html.twig',
                self::EDIT => 'admin/trabajador/edit.html.twig',
            ],
            'routes' => [
                self::INDEX => 'admin_trabajador_index',
                self::NEW => 'admin_trabajador_new',
                self::EDIT => 'admin_trabajador_edit',
                self::DELETE => 'admin_trabajador_delete',
            ],
        ];
    }


    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        # Comprobando si el trabajador tiene acceso a esta opción
        $this->denyAccessUnlessGranted([], $request);

        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        if (in_array('ROLE_ADMIN', $credencial->getRoles())) {
            $trabajadores = $this->trabajadorRepository->findAll();
        } else {
            # Obtengo la lista de estructura subordinada a la principal
            $estructuras = $this->estructuraRepository->getChildren($credencial->getTrabajador()->getEstructura());

            # Agrego la estructura principal a la lista de subordinadas
            $estructuras[] = $credencial->getTrabajador()->getEstructura();

            # Obtengo la lista de trabajadores de las lista de estructuras
            $trabajadores = $this->trabajadorRepository->findByEstructuras($estructuras, [$credencial->getTrabajador()]);
        }

        $settings = $this->settings();
        $pagination = $this->paginator->paginate(
            $trabajadores,
            $request->query->getInt('page', 1),
            $settings['page']['limit']
        );

        return $this->render($settings['templates'][self::INDEX], [
            'pagination' => $pagination,
        ]);
    }
}
