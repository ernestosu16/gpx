<?php

namespace App\Controller\Admin;

use App\Controller\_Controller_;
use App\Entity\Trabajador;
use App\Form\Admin\TrabajadorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/trabajador', name: 'admin_trabajador_')]
class TrabajadorController extends _Controller_
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $trabajadors = $this->getDoctrine()
            ->getRepository(Trabajador::class)
            ->findAll();

        return $this->render('admin/trabajador/index.html.twig', [
            'trabajadors' => $trabajadors,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $trabajador = new Trabajador();
        $form = $this->createForm(TrabajadorType::class, $trabajador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trabajador);
            $entityManager->flush();

            return $this->redirectToRoute('admin_trabajador_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/trabajador/new.html.twig', [
            'trabajador' => $trabajador,
            'form' => $form,
        ]);
    }

    #[Route('/{trabajador}', name: 'show', methods: ['GET'])]
    public function show(Trabajador $trabajador): Response
    {
        return $this->render('admin/trabajador/show.html.twig', [
            'trabajador' => $trabajador,
        ]);
    }

    #[Route('/{trabajador}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trabajador $trabajador): Response
    {
        $form = $this->createForm(TrabajadorType::class, $trabajador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_trabajador_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/trabajador/edit.html.twig', [
            'trabajador' => $trabajador,
            'form' => $form,
        ]);
    }

    #[Route('/{trabajador}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Trabajador $trabajador): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trabajador);
        $entityManager->flush();

        return $this->redirectToRoute('admin_trabajador_index', [], Response::HTTP_SEE_OTHER);
    }
}
