<?php

namespace App\Controller\Admin;

use App\Config\Nomenclador\Grupo as GrupoNomenclador;
use App\Entity\Grupo;
use App\Form\Admin\GrupoType;
use App\Repository\GrupoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/grupo')]
class GrupoController extends _AdminController_
{
    #[Route('/', name: 'grupo_index', methods: ['GET'])]
    public function index(): Response
    {
        $grupos = $this->getDoctrine()
            ->getRepository(Grupo::class)
            ->findOneBy(['codigo' => (string)GrupoNomenclador::newInstance()]);

        return $this->render('admin/grupo/index.html.twig', [
            'grupos' => $grupos->getChildren(),
        ]);
    }

    #[Route('/new', name: 'grupo_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $grupo = new Grupo();
        $form = $this->createForm(GrupoType::class, $grupo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var GrupoRepository $nomencladorRepository */
            $nomencladorRepository = $entityManager->getRepository(Grupo::class);
            $parent = $nomencladorRepository->findOneByCodigo((string)GrupoNomenclador::newInstance());
            $grupo->setParent($parent);

            $entityManager->persist($grupo);
            $entityManager->flush();

            return $this->redirectToRoute('grupo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/grupo/new.html.twig', [
            'grupo' => $grupo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'grupo_show', methods: ['GET'])]
    public function show(Grupo $grupo): Response
    {
        return $this->render('admin/grupo/show.html.twig', [
            'grupo' => $grupo,
        ]);
    }

    #[Route('/{id}/edit', name: 'grupo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Grupo $grupo): Response
    {
        $form = $this->createForm(GrupoType::class, $grupo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('grupo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/grupo/edit.html.twig', [
            'grupo' => $grupo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'grupo_delete', methods: ['POST'])]
    public function delete(Request $request, Grupo $grupo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $grupo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($grupo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('grupo_index', [], Response::HTTP_SEE_OTHER);
    }
}
