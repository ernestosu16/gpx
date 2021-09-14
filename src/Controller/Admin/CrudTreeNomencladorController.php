<?php

namespace App\Controller\Admin;

use App\Entity\Nomenclador;
use App\Form\Admin\NomencladorType;
use App\Repository\_Repository_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class CrudTreeNomencladorController extends CrudController
{

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var _Repository_ $repository */
        $repository = $this->getDoctrine()->getRepository(static::entity());

        if (!$repository instanceof Nomenclador)
            $this->createNotFoundException('Error la entidad no es una instancia de "Nomenclador"');

        /** @var Nomenclador $nomenclador */
        $nomenclador = $repository->findOneBy(['codigo' => $this->getCode()]);

        if (!$nomenclador)
            $this->createNotFoundException(printf(
                'Error no se encontró el nomenclador padre "%s"', $this->getCode()
            ));

        return $this->render($this->getTemplate(self::INDEX), [
            'title' => $this->getTitle(self::INDEX),
            'config' => $this->getConfig(),
            'nomencladores' => $nomenclador->getChildren(),
        ]);
    }

    #[Route('/parent/{codigo}', name: '_child_index', methods: ['GET'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(codigo)')]
    public function childIndex(Nomenclador $nomencladorParent): Response
    {
        return $this->render(
            'admin/nomenclador/child/index.html.twig',
            ['nomencladorParent' => $nomencladorParent, 'nomencladors' => $nomencladorParent->getChildren()]
        );
    }

    #[Route('/parent/{codigo}/new', name: '_child_new', methods: ['GET', 'POST'])]
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


    #[Route('/parent/{codigo}/{id}', name: '_child_show', methods: ['GET'])]
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


    #[Route('/parent/{codigo}/{id}/edit', name: '_child_edit', methods: ['GET', 'POST'])]
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


    #[Route('/parent/{codigo}/{id}', name: '_child_delete', methods: ['POST'])]
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
