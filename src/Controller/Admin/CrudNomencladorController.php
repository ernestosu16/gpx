<?php

namespace App\Controller\Admin;

use App\Entity\Nomenclador;
use App\Repository\_Repository_;
use App\Repository\NomencladorRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class CrudNomencladorController extends CrudController
{
    protected array $template = [
        self::INDEX => 'admin/crud/nomenclador/index.html.twig',
        self::NEW => 'admin/crud/nomenclador/new.html.twig',
        self::EDIT => 'admin/crud/nomenclador/edit.html.twig',
        self::SHOW => 'admin/crud/nomenclador/show.html.twig',
    ];

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var _Repository_ $repository */
        $repository = $this->getDoctrine()->getRepository(static::entity());

        if (!$repository instanceof EntityRepository)
            throw $this->createNotFoundException('Error la entidad no es una instancia de "Nomenclador"');

        /** @var Nomenclador $nomenclador */
        $nomenclador = $repository->findOneBy(['codigo' => $this->getCode()]);

        if (!$nomenclador)
            throw $this->createNotFoundException(printf(
                'Error no se encontró el nomenclador padre "%s"', $this->getCode()
            ));

        return $this->render($this->getTemplate(self::INDEX), [
            'title' => $this->getTitle(self::INDEX),
            'config' => $this->getConfig(),
            'nomencladores' => $nomenclador->getChildren(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $class = static::entity();
        $nomenclador = new $class();
        $form = $this->createForm(static::formType(), $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var NomencladorRepository $nomencladorRepository */
            $nomencladorRepository = $entityManager->getRepository(static::entity());
            $parent = $nomencladorRepository->findOneByCodigo($this->getCode());
            $nomenclador->setParent($parent);

            $entityManager->persist($nomenclador);
            $entityManager->flush();

            return $this->redirectToRoute($this->getRoute(self::INDEX), [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm($this->getTemplate(self::NEW), [
            'title' => $this->getTitle(self::EDIT),
            'nomenclador' => $nomenclador,
            'form' => $form,
            'config' => $this->getConfig(),
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nomenclador $nomenclador): Response
    {
        $form = $this->createForm(static::formType(), $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute($this->getRoute(self::INDEX), [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm($this->getTemplate(self::EDIT), [
            'title' => $this->getTitle(self::EDIT),
            'nomenclador' => $nomenclador,
            'form' => $form,
            'config' => $this->getConfig(),
        ]);
    }

    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Nomenclador $id): Response
    {
        return $this->render($this->getTemplate(self::SHOW), [
            'title' => $this->getTitle(self::SHOW),
            'nomenclador' => $id,
            'config' => $this->getConfig(),
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Nomenclador $grupo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $grupo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($grupo);
            $entityManager->flush();
        }

        return $this->redirectToRoute($this->getRoute(self::INDEX), [], Response::HTTP_SEE_OTHER);
    }
}
