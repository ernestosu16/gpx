<?php

namespace App\Controller\Admin;

use App\Controller\_Controller_;
use App\Entity\Estructura;
use App\Form\Admin\EstructuraType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/estructura', name: 'admin_estructura_')]
class EstructuraController extends _Controller_
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $estructuras = $this->getDoctrine()->getRepository(Estructura::class)->findAll();

        return $this->render('admin/estructura/index.html.twig', [
            'estructuras' => $estructuras,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $trabajador = new Estructura();
        $form = $this->createForm(EstructuraType::class, $trabajador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trabajador);
            $entityManager->flush();

            return $this->redirectToRoute('admin_estructura_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/estructura/new.html.twig', [
            'trabajador' => $trabajador,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Estructura $estructura): Response
    {
        $form = $this->createForm(EstructuraType::class, $estructura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_estructura_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/estructura/edit.html.twig', [
            'estructura' => $estructura,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Estructura $estructura): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($estructura);
        $entityManager->flush();

        return $this->redirectToRoute('admin_estructura_index', [], Response::HTTP_SEE_OTHER);
    }
}
