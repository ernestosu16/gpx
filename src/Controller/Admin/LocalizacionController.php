<?php

namespace App\Controller\Admin;

use App\Controller\_Controller_;
use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use App\Form\Admin\LocalizacionType;
use App\Repository\LocalizacionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/localizacion', name: 'admin_localizacion_')]
class LocalizacionController extends _Controller_
{
    #[Route('/{tipo}/{parent}', name: 'index', defaults: ['tipo' => LocalizacionTipo::PROVINCIA, 'parent' => null], methods: ['GET'])]
    #[Entity(data: 'tipo', expr: 'repository.findOneByCodigo(tipo)', class: LocalizacionTipo::class)]
    #[Entity(data: 'parent', expr: 'repository.findOneByCodigo(parent)', class: Localizacion::class)]
    public function index(LocalizacionTipo $tipo, ?Localizacion $parent): Response
    {
        dump($parent);
        dump($tipo);
        /** @var LocalizacionRepository $localizacionRepository */
        $localizacionRepository = $this->getDoctrine()->getRepository(Localizacion::class);

        return $this->render('admin/localizacion/index.html.twig', [
            'tipo' => $tipo,
            'parent' => $parent,
            'localizaciones' => $localizacionRepository->findByTipoAndParent($tipo, $parent),
        ]);
    }


    #[Route('/{tipo}/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $localizacion = new Localizacion();
        $form = $this->createForm(LocalizacionType::class, $localizacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($localizacion);
            $entityManager->flush();

            return $this->redirectToRoute('admin_localizacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/localizacion/new.html.twig', [
            'trabajador' => $localizacion,
            'form' => $form,
        ]);
    }

//    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Localizacion $trabajador): Response
//    {
//        $form = $this->createForm(LocalizacionType::class, $trabajador);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('admin_localizacion_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('admin/localizacion/edit.html.twig', [
//            'trabajador' => $trabajador,
//            'form' => $form,
//        ]);
//    }
}
