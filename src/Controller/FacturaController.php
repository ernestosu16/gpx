<?php


namespace App\Controller;


use App\Repository\NomencladorRepository;
use App\Repository\SacaRepository;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/factura/find-sacas-factura', name: 'find_sacas_factura', options: ["expose" => true] ,methods: ['POST'])]
    public function findSacasFactura(Request $request)
    {
        $noFactura = $request->get('noFactura');
        $sacas = $this->sacaRepository->findSacasNoFactura($noFactura);
        $anomalias = $this->nomencladorRepository->findByChildren('APP_SACA_ANOMALIA');
/*
        $serializer = SerializerBuilder::create()->build();
        $miRespuestaJson = $serializer->serialize($sacas,"json");

        return JsonResponse::fromJsonString($miRespuestaJson);*/

        $html = $this->renderView('factura/sacas.html.twig', ['sacas'=>$sacas, 'anomalias'=>$anomalias->toArray()]);

        return new Response($html);
    }
}