<?php

namespace App\Controller\Admin;

use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use App\Form\Admin\TrabajadorType;
use App\Repository\EstructuraRepository;
use App\Repository\TrabajadorRepository;
use App\Service\NotifyService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Pure;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/trabajador', name: 'admin_trabajador')]
final class TrabajadorController extends _CrudController_
{
    #[Pure] public function __construct(
        protected ManagerRegistry    $managerRegistry,
        protected PaginatorInterface $paginator,
        protected NotifyService      $notify,
        private TrabajadorRepository $trabajadorRepository,
        private EstructuraRepository $estructuraRepository,
    )
    {
        parent::__construct($managerRegistry, $paginator, $notify);
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


        $settings = $this->settings();
        $pagination = $this->paginator->paginate(
            $this->getTrabajadores(),
            $request->query->getInt('page', 1),
            $settings['page']['limit']
        );

        return $this->render($settings['templates'][self::INDEX], [
            'pagination' => $pagination,
        ]);
    }

    private function getTrabajadores(): QueryBuilder
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        $query = $this->trabajadorRepository->createQueryBuilder('t')
            ->join('t.credencial', 'c')
            ->join('t.persona', 'p')
            ->join('t.estructura', 'e');

        if (in_array('ROLE_ADMIN', $credencial->getRoles()))
            return $query;

        # Obtengo la lista de estructura subordinada a la principal
        $estructuras = $this->estructuraRepository->getChildren($credencial->getTrabajador()->getEstructura());

        # Agrego la estructura principal a la lista de subordinadas
        $estructuras[] = $credencial->getTrabajador()->getEstructura();

        $query
            ->where('t != :trabajador')->setParameter('trabajador', $credencial->getTrabajador())
            ->andWhere('e IN (:estructuras)')->setParameter('estructuras', $estructuras);


        return $query;
    }

    #[Route('/{id}/remove', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted([], $request);
        $entity = $this->trabajadorRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $entity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->managerRegistry->getManager();
            try {
                $entityManager->remove($entity);
                $entityManager->flush();
                $this->notify->toastr()->success('Eliminado correctamente.', 'Eliminado');
            } catch (ForeignKeyConstraintViolationException $exception) {
                $this->notify->toastr()->error($exception->getMessage(), 'Eliminado');
            }

        }

        $settings = $this->settings();
        return $this->redirectToRoute($settings['routes'][self::INDEX], [], Response::HTTP_SEE_OTHER);
    }
}
