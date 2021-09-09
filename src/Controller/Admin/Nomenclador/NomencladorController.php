<?php

namespace App\Controller\Admin\Nomenclador;

use App\Entity\Nomenclador;
use App\Form\NomencladorType;
use App\Repository\NomencladorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/nomenclador')]
class NomencladorController extends AbstractController
{
    static function Parent(): string
    {
        return 'APP_GRUPO';
    }

    #[Route('/', name: 'nomenclador_index', methods: ['GET'])]
    public function index(NomencladorRepository $nomencladorRepository): Response
    {
        $nomencladors = $nomencladorRepository->findOneByCodigo(static::Parent());
        return $this->render('nomenclador/index.html.twig', [
            'nomencladors' => $nomencladors->getChildren(),
        ]);
    }

    #[Route('/new', name: 'nomenclador_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $nomenclador = new Nomenclador();
        $form = $this->createForm(NomencladorType::class, $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nomenclador);
            $entityManager->flush();

            return $this->redirectToRoute('nomenclador_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nomenclador/new.html.twig', [
            'nomenclador' => $nomenclador,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'nomenclador_show', methods: ['GET'])]
    public function show(Nomenclador $nomenclador): Response
    {
        return $this->render('nomenclador/show.html.twig', [
            'nomenclador' => $nomenclador,
        ]);
    }

    #[Route('/{id}/edit', name: 'nomenclador_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nomenclador $nomenclador): Response
    {
        $form = $this->createForm(NomencladorType::class, $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nomenclador_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nomenclador/edit.html.twig', [
            'nomenclador' => $nomenclador,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'nomenclador_delete', methods: ['POST'])]
    public function delete(Request $request, Nomenclador $nomenclador): Response
    {
        if ($this->isCsrfTokenValid('delete' . $nomenclador->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nomenclador);
            $entityManager->flush();
        }

        return $this->redirectToRoute('nomenclador_index', [], Response::HTTP_SEE_OTHER);
    }
}
