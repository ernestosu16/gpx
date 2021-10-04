<?php

namespace App\Controller\Admin;

use App\Entity\Estructura;
use App\Entity\TrabajadorCredencial;
use App\Form\Admin\EstructuraType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/estructura', name: 'admin_estructura')]
final class EstructuraController extends _CrudController_
{
    protected static function entity(): string
    {
        return Estructura::class;
    }

    protected static function formType(): string
    {
        return EstructuraType::class;
    }

    protected static function config(): array
    {
        return [
            'titles' => [
                self::INDEX => 'Listado de las estructuras',
            ],
            'templates' => [
                self::INDEX => 'admin/estructura/index.html.twig',
            ],
            'routes' => [
                self::INDEX => 'admin_estructura_index',
                self::NEW => 'admin_estructura_new',
                self::EDIT => 'admin_estructura_edit',
                self::DELETE => 'admin_estructura_delete',
            ]
        ];
    }

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted([], $request);

        $settings = $this->settings();
        return $this->render($settings['templates'][self::INDEX], [
            'settings' => $settings,
            'collection' => $this->getEstructuras(),
        ]);
    }

    private function getEstructuras(): array
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        if (in_array('ROLE_ADMIN', $credencial->getRoles()))
            return $this->getDoctrine()->getRepository(Estructura::class)->findAll();

        # Obtengo la lista de estructura subordinadas a la principal
        return array_merge(
            [$credencial->getTrabajador()->getEstructura()],
            $this->getDoctrine()->getRepository(Estructura::class)->getChildren(
                $credencial->getTrabajador()->getEstructura()
            )
        );
    }
}
