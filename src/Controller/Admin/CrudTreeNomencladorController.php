<?php

namespace App\Controller\Admin;

use App\Config\Nomenclador\_Nomenclador_;
use App\Entity\Nomenclador;
use App\Repository\_Repository_;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class CrudTreeNomencladorController extends CrudController
{
    abstract static protected function parent(): _Nomenclador_;

    protected function getParent(): _Nomenclador_
    {
        return static::parent();
    }

    protected array $template = [
        self::INDEX => 'admin/crud/tree/index.html.twig',
        self::NEW => 'admin/crud/tree/new.html.twig',
        self::EDIT => 'admin/crud/tree/edit.html.twig',
        self::SHOW => 'admin/crud/tree/show.html.twig',
    ];

    #[Route('/{parent}', name: '_index', defaults: ['parent' => ''], methods: ['GET'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(parent)')]
    public function index(?Nomenclador $nomencladorParent): Response
    {
        /** @var _Repository_ $repository */
        $repository = $this->getDoctrine()->getRepository(static::entity());

        if (!$repository instanceof EntityRepository)
            throw $this->createNotFoundException('Error la entidad no es una instancia de "Nomenclador"');

        if ($nomencladorParent) {
            $nomenclador = $nomencladorParent;
        } else {
            /** @var Nomenclador $nomenclador */
            $nomenclador = $repository->findOneBy(['codigo' => $this->getParent()->getCode()]);
            if (!$nomenclador) {
                throw $this->createNotFoundException(sprintf(
                    'Error no se encontró "%s" el nomenclador padre. Revise la configuración.',
                    $this->getParent()->getCode()));
            }
        }

        return $this->render($this->getTemplate(self::INDEX), [
            'title' => $this->getTitle(self::INDEX) . ' (' . $nomencladorParent?->getNombre() . ')',
            'config' => $this->getConfig(),
            'nomencladorParent' => $nomencladorParent ?? $nomenclador,
            'nomencladores' => $nomenclador->getChildren(),
        ]);
    }


    #[Route('/{parent}/new', name: '_new', defaults: ['parent' => ''], methods: ['GET', 'POST'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(parent)')]
    public function childNew(Request $request, ?Nomenclador $nomencladorParent): Response
    {
        $class = static::entity();
        $menu = new $class();
        $form = $this->createForm(static::formType(), $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $menu->setParent($nomencladorParent);

            $entityManager->persist($menu);
            $entityManager->flush();

            return $this->redirectToRoute($this->getRoute(self::INDEX), [
                'parent' => $nomencladorParent->getCodigo()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm($this->getTemplate(self::NEW), [
            'config' => $this->getConfig(),
            'title' => $this->getTitle(self::NEW),
            'nomencladorParent' => $nomencladorParent ?? $this->getParent(),
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{parent}/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(parent)')]
    public function childEdit(Request $request, Nomenclador $nomencladorParent, Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== $nomencladorParent->getCodigo())
            throw $this->createNotFoundException('El nomenclador no existe');

        $form = $this->createForm(static::formType(), $nomenclador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                $this->getRoute(self::INDEX),
                ['parent' => $nomencladorParent->getCodigo()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm($this->getTemplate(self::EDIT), [
            'config' => $this->getConfig(),
            'title' => $this->getTitle(self::EDIT),
            'nomencladorParent' => $nomencladorParent,
            'nomenclador' => $nomenclador,
            'form' => $form,
        ]);
    }

    #[Route('/{parent}/{id}', name: '_show', methods: ['GET'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(parent)')]
    public function childShow(Nomenclador $nomencladorParent, Nomenclador $nomenclador): Response
    {
        if ($nomenclador->getParent()->getCodigo() !== $nomencladorParent->getCodigo())
            throw $this->createNotFoundException('El nomenclador no existe');

        return $this->render($this->getTemplate(self::SHOW), [
            'config' => $this->getConfig(),
            'title' => $this->getTitle(self::SHOW),
            'nomencladorParent' => $nomencladorParent,
            'nomenclador' => $nomenclador
        ]);
    }

    #[Route('/{parent}/{id}', name: '_delete', methods: ['POST'])]
    #[Entity(data: 'nomencladorParent', expr: 'repository.findOneByCodigo(parent)')]
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
            $this->getRoute(self::INDEX),
            ['parent' => $nomencladorParent->getCodigo()],
            Response::HTTP_SEE_OTHER
        );
    }

}
