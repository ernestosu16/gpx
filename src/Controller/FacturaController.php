<?php


namespace App\Controller;


use App\Repository\NomencladorRepository;
use App\Repository\SacaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/factura')]
class FacturaController extends AbstractController
{
    public function __construct(
        private SacaRepository $sacaRepository,
        private NomencladorRepository $nomencladorRepository
    )
    {
    }

    #[Route('/procesar', name: 'procesar_factura', methods: ['GET'])]
    public function procesarFactura()
    {
        $anomalias = $this->nomencladorRepository->findByChildren('APP_SACA_ANOMALIA');
        return $this->render('factura/procesarFactura.html.twig',[
            "anomalias" => $anomalias,
        ]);
    }

    #[Route('/find-sacas-factura', name: 'find_sacas_factura', methods: ['POST'])]
    public function findSacasFactura(Request $request)
    {
        $noFactura = $request->get('noFactura');

        return $this->sacaRepository->findSacasNoFactura($noFactura);

    }
}