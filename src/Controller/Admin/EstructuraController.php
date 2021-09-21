<?php

namespace App\Controller\Admin;

use App\Controller\_Controller_;
use App\Entity\Estructura;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/estructura', name: 'admin_estructura_')]
class EstructuraController extends _Controller_
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $estructuras = $this->getDoctrine()
            ->getRepository(Estructura::class)->findAll();

        return $this->render('admin/estructura/index.html.twig', [
            'estructuras' => $estructuras,
        ]);
    }
}
