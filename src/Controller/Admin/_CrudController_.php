<?php

namespace App\Controller\Admin;

use App\Controller\_Controller_;
use App\Service\NotifyService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class _CrudController_ extends _Controller_
{
    const INDEX = 'index';
    const NEW = 'new';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const SHOW = 'show';

    private string $translation_domain = 'admin';

    private array $titles = [
        self::INDEX => 'Lista',
        self::NEW => 'Nuevo',
        self::EDIT => 'Editar',
        self::SHOW => 'Mostrar',
    ];

    private array $templates = [
        self::INDEX => 'admin/crud/index.html.twig',
        self::NEW => 'admin/crud/new.html.twig',
        self::EDIT => 'admin/crud/edit.html.twig',
        self::SHOW => null
    ];

    private array $routes = [
        self::INDEX => null,
        self::NEW => null,
        self::EDIT => null,
        self::SHOW => null,
        self::DELETE => null
    ];

    private array $page = ['limit' => 20, 'orderBy' => []];

    public function __construct(
        protected ManagerRegistry    $managerRegistry,
        protected PaginatorInterface $paginator,
        protected NotifyService      $notify,
    )
    {
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services['doctrine.orm.entity_manager'] = '?' . EntityManagerInterface::class;
        return $services;
    }

    protected static function fields(): array
    {
        return [];
    }

    protected static function parentCode(): ?string
    {
        return null;
    }

    abstract protected static function entity(): string;

    abstract protected static function formType(): string;

    abstract protected static function config(): array;

    protected function settings(): array
    {
        $fields = static::fields();
        if (empty($fields)) {
            $metadata = $this->managerRegistry->getManager()->getClassMetadata(static::entity());
            /** @var ReflectionProperty $field */
            foreach ($metadata->reflFields as $field) {
                if ($field->getName() === 'id') continue;
                $fields[] = $field->getName();
            }
        }

        return array_replace_recursive([
            'translation_domain' => $this->translation_domain,
            'titles' => $this->titles,
            'templates' => $this->templates,
            'routes' => $this->routes,
            'fields' => $fields,
            'page' => $this->page,
        ], static::config());
    }

    protected function getTitle(string $key): string
    {
        $settings = $this->settings();
        $titles = $settings['titles'];
        return $titles[$key];
    }

    protected function getTemplate(string $key): string
    {
        $settings = $this->settings();
        return $settings['templates'][$key];
    }

    protected function getRoute(string $key): string
    {
        $settings = $this->settings();
        return $settings['routes'][$key];
    }

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted([], $request);
        $settings = $this->settings();

        $query = $this->managerRegistry
            ->getRepository(static::entity())
            ->createQueryBuilder('q');

        $filters = [];
        if (isset($settings['filter']))
            $filters = $settings['filter'];

        if (static::parentCode())
            $query->andWhere('q.parent is not null');

        $pagination = $this->paginator->paginate($query,
            $request->query->getInt('page', 1),
            $settings['page']['limit']
        );

        return $this->render($settings['templates'][self::INDEX], [
            'filters' => $filters,
            'settings' => $settings,
            'pagination' => $pagination
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted([], $request);
        $settings = $this->settings();
        $class = static::entity();
        $entity = new $class();
        $form = $this->createForm(static::formType(), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (static::parentCode()) {
                $parent = $this->managerRegistry->getManager()->getRepository($class)->findOneByCodigo(static::parentCode());
                if (!$parent)
                    throw new ORMInvalidArgumentException(sprintf("No se encontró el padre el código \"%s\" buscado", static::parentCode()));
                $entity->setParent($parent);
            }

            $this->managerRegistry->getManager()->persist($entity);
            $this->managerRegistry->getManager()->flush();

            $this->notify->toastr()->success('Datos creado correctamente.', 'Creado');
            return $this->redirectToRoute($settings['routes'][self::INDEX], [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm($settings['templates'][self::NEW], [
            'entity' => $entity,
            'form' => $form,
            'settings' => $settings,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $id): Response
    {
        $this->denyAccessUnlessGranted([], $request);
        $entity = $this->managerRegistry->getRepository(static::entity())->find($id);
        $settings = $this->settings();
        $form = $this->createForm(static::formType(), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $form->getData();
            $this->managerRegistry->getManager()->persist($entity);
            $this->managerRegistry->getManager()->flush();

            $this->notify->toastr()->success('Editado correctamente.', 'Editar');
            return $this->redirectToRoute($settings['routes'][self::INDEX], [], Response::HTTP_SEE_OTHER);
        }

        return $this->render($settings['templates'][self::EDIT], [
            'settings' => $this->settings(),
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/remove', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted([], $request);
        $entity = $this->managerRegistry->getRepository(static::entity())->find($id);
        if ($this->isCsrfTokenValid('delete' . $entity->getId(), $request->request->get('_token'))) {
            try {
                $entityManager = $this->managerRegistry->getManager();
                $entityManager->remove($entity);
                $entityManager->flush();
                $this->notify->toastr()->success('Eliminado', 'Eliminado correctamente.');
            } catch (ForeignKeyConstraintViolationException $constraintViolationException) {
                $this->notify->toastr()->error($constraintViolationException->getMessage());
            }

        }

        $settings = $this->settings();
        return $this->redirectToRoute($settings['routes'][self::INDEX], [], Response::HTTP_SEE_OTHER);
    }
}
