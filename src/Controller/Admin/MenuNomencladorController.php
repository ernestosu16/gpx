<?php

namespace App\Controller\Admin;

use App\Config\Nomenclador\Menu as MenuNomenclador;
use App\Controller\_Controller_;
use App\Entity\Menu;
use App\Form\Admin\MenuType;
use App\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/menu')]
class MenuNomencladorController extends _Controller_
{
    #[Route('/', name: 'menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        $menu = $menuRepository->findOneByCodigo(MenuNomenclador::code());
        return $this->render('admin/menu/index.html.twig', [
            'menus' => $menu->getChildren(),
        ]);
    }

    #[Route('/new', name: 'menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /** @var MenuRepository $nomencladorRepository */
            $nomencladorRepository = $entityManager->getRepository(Menu::class);
            $parent = $nomencladorRepository->findOneByCodigo((string)MenuNomenclador::newInstance());
            $menu->setParent($parent);

            $entityManager->persist($menu);
            $entityManager->flush();

            return $this->redirectToRoute('menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        return $this->render('admin/menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/edit', name: 'menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu): Response
    {
        if ($this->isCsrfTokenValid('delete' . $menu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($menu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('menu_index', [], Response::HTTP_SEE_OTHER);
    }
}
