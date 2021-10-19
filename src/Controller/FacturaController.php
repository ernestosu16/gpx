<?php


namespace App\Controller;


use App\Repository\FacturaRepository;
use App\Repository\NomencladorRepository;
use App\Repository\SacaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/factura')]
class FacturaController extends AbstractController
{
    public function __construct(
        private SacaRepository $sacaRepository,
        private NomencladorRepository $nomencladorRepository,
        private FacturaRepository $facturaRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/procesar', name: 'procesar_factura', methods: ['GET'])]
    public function procesarFactura()
    {

        return $this->render('factura/procesarFactura.html.twig',[]);
    }

    #[Route('/find-sacas-factura', name: 'find_sacas_factura', options: ["expose" => true] ,methods: ['POST'])]
    public function findSacasFactura(Request $request)
    {
        $noFactura = $request->get('noFactura');
        $sacas = $this->facturaRepository->findSacasNoFacturaAndEstado($noFactura);
        $anomalias = $this->nomencladorRepository->findByChildren('APP_SACA_ANOMALIA');

        $html = $sacas ? $this->renderView('factura/sacas.html.twig', [
            'sacas'=>$sacas,
            'anomalias'=>$anomalias->toArray(),
            'noFactura' => $noFactura]) : 'No se encontro factura con ese numero factura';

        return new Response($html);
    }

    #[Route('/recepcionar-sacas-factura', name: 'recepcionar_sacas_factura', options: ["expose" => true] ,methods: ['POST'])]
    public function recepcionarSacasFactura(Request $request)
    {
        $noFactura = $request->get('noFactura');
        $sacas = $request->get('sacas');
        $todos = $request->get('todos');
        $factura = $this->facturaRepository->getFacturaByNoFactura($noFactura);
        $estado = $this->nomencladorRepository->findOneByCodigo('APP_SACA_ESTADO_RECIBIDA');

        foreach ($sacas as $id)
        {
            $saca = $this->sacaRepository->find($id);
            $saca->setEstado($estado);

            $this->entityManager->persist($saca);
            $this->entityManager->flush();
        }

        if($todos)
        {
            $estado = $this->nomencladorRepository->findOneByCodigo('APP_FACTURA_ESTADO_RECIBIDA');
            $factura->setEstado($estado);
            $this->entityManager->persist($factura);
            $this->entityManager->flush();
        }
        return $this->render('factura/procesarFactura.html.twig',[]);
    }
}