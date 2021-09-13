<?php

namespace App\Controller\Admin;

use App\Config\Nomenclador\App;
use App\Entity\Nomenclador;
use App\Form\Admin\NomencladorType;
use App\Repository\NomencladorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/nomenclador')]
class NomencladorController extends AbstractController
{
    #[Route('/', name: 'nomenclador_index', methods: ['GET'])]
    public function index(NomencladorRepository $nomencladorRepository): Response
    {
        return $this->render('admin/nomenclador/index.html.twig', [
            'nomencladors' => $nomencladorRepository->findByChildren(App::code()),
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

            /** @var NomencladorRepository $nomencladorRepository */
            $nomencladorRepository = $entityManager->getRepository(Nomenclador::class);
            $nomencladorParent = $nomencladorRepository->findOneByCodigo(App::code());
            $nomencladorParent->addChild($nomenclador);

            $entityManager->persist($nomencladorParent);
            $entityManager->flush();

            return $this->redirectToRoute('nomenclador_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/nomenclador/new.html.twig', [
            'nomenclador' => $nomenclador,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'nomenclador_show', methods: ['GET'])]
    public function show(Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== App::code())
            throw $this->createNotFoundException('El nomenclador no existe');

        return $this->render('admin/nomenclador/show.html.twig', [
            'nomenclador' => $nomenclador,
        ]);
    }

    #[Route('/{id}/edit', name: 'nomenclador_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== App::code())
            throw $this->createNotFoundException('El nomenclador no existe');

        $form = $this->createForm(NomencladorType::class, $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nomenclador_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/nomenclador/edit.html.twig', [
            'nomenclador' => $nomenclador,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'nomenclador_delete', methods: ['POST'])]
    public function delete(Request $request, Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== App::code())
            throw $this->createNotFoundException('El nomenclador no existe');

        if ($this->isCsrfTokenValid('delete' . $nomenclador->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nomenclador);
            $entityManager->flush();
        }

        return $this->redirectToRoute('nomenclador_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/parent/{codigo}', name: 'nomenclador_child_index', methods: ['GET'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(codigo)')]
    public function childIndex(Nomenclador $nomencladorParent): Response
    {
        return $this->render(
            'admin/nomenclador/child/index.html.twig',
            ['nomencladorParent' => $nomencladorParent, 'nomencladors' => $nomencladorParent->getChildren()]
        );
    }

    #[Route('/parent/{codigo}/new', name: 'nomenclador_child_new', methods: ['GET', 'POST'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(codigo)')]
    public function childNew(Nomenclador $nomencladorParent, Request $request): Response
    {
        $nomenclador = new Nomenclador();
        $form = $this->createForm(NomencladorType::class, $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $nomencladorParent->addChild($nomenclador);
            $entityManager->persist($nomencladorParent);
            $entityManager->flush();

            return $this->redirectToRoute('nomenclador_child_index', ['codigo' => $nomencladorParent->getCodigo()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'admin/nomenclador/child/new.html.twig',
            ['nomencladorParent' => $nomencladorParent, 'nomenclador' => $nomenclador, 'form' => $form]
        );
    }


    #[Route('/parent/{codigo}/{id}', name: 'nomenclador_child_show', methods: ['GET'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(codigo)')]
    public function childShow(Nomenclador $nomencladorParent, Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== $nomencladorParent->getCodigo())
            throw $this->createNotFoundException('El nomenclador no existe');

        return $this->render(
            'admin/nomenclador/child/show.html.twig',
            ['nomencladorParent' => $nomencladorParent, 'nomenclador' => $nomenclador]
        );
    }


    #[Route('/parent/{codigo}/{id}/edit', name: 'nomenclador_child_edit', methods: ['GET', 'POST'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(codigo)')]
    public function childEdit(Request $request, Nomenclador $nomencladorParent, Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== $nomencladorParent->getCodigo())
            throw $this->createNotFoundException('El nomenclador no existe');

        $form = $this->createForm(NomencladorType::class, $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'nomenclador_child_index',
                ['codigo' => $nomencladorParent->getCodigo()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('admin/nomenclador/child/edit.html.twig', [
            'nomencladorParent' => $nomencladorParent,
            'nomenclador' => $nomenclador,
            'form' => $form,
        ]);
    }


    #[Route('/parent/{codigo}/{id}', name: 'nomenclador_child_delete', methods: ['POST'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(codigo)')]
    public function childDelete(Request $request, Nomenclador $nomencladorParent, Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== $nomencladorParent->getCodigo())
            throw $this->createNotFoundException('El nomenclador no existe');

        if ($this->isCsrfTokenValid('delete' . $nomenclador->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nomenclador);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'nomenclador_child_index',
            ['codigo' => $nomencladorParent->getCodigo()],
            Response::HTTP_SEE_OTHER
        );
    }
}
