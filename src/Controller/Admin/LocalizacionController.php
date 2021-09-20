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
final class LocalizacionController extends _Controller_
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
}
