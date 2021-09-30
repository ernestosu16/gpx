<?php

namespace App\Controller\Admin;

use App\Controller\_Controller_;
use App\Entity\Estructura;
use App\Entity\Trabajador;
use App\Entity\TrabajadorCredencial;
use App\Form\Admin\TrabajadorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/trabajador', name: 'admin_trabajador_')]
final class TrabajadorController extends _Controller_
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        # Comprobando si el trabajador tiene acceso a esta opción
        $this->denyAccessUnlessGranted([], $request);

        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        # Obtengo la lista de estructura subordinadas a la principal
        $estructuras = $this->getDoctrine()->getRepository(Estructura::class)->getChildren(
            $credencial->getTrabajador()->getEstructura()
        );

        # Agrego la estructura principal a la lista de subordinadas
        $estructuras[] = $credencial->getTrabajador()->getEstructura();

        # Obtengo la lista de trabajadores de las lista de estructuras
        $trabajadores = $this->getDoctrine()->getRepository(Trabajador::class)->findByEstructuras(
            $estructuras
        );

        return $this->render('admin/trabajador/index.html.twig', [
            'trabajadores' => $trabajadores,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        # Comprobando si el trabajador tiene acceso a esta opción
        $this->denyAccessUnlessGranted([], $request);

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
    public function show(Request $request, Trabajador $trabajador): Response
    {
        $this->denyAccessUnlessGranted([], $request);
        return $this->render('admin/trabajador/show.html.twig', [
            'trabajador' => $trabajador,
        ]);
    }

    #[Route('/{trabajador}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trabajador $trabajador): Response
    {
        $this->denyAccessUnlessGranted([], $request);

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
        $this->denyAccessUnlessGranted([], $request);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trabajador);
        $entityManager->flush();

        return $this->redirectToRoute('admin_trabajador_index', [], Response::HTTP_SEE_OTHER);
    }
}
