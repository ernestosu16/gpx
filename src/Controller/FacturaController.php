<?php


namespace App\Controller;


use App\Entity\TrabajadorCredencial;
use App\Manager\EnvioManager;
use App\Manager\FacturaManager;
use App\Repository\EnvioRepository;
use App\Repository\FacturaRepository;
use App\Repository\NomencladorRepository;
use App\Repository\SacaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/factura')]
class FacturaController extends AbstractController
{
    public function __construct(
        private EnvioManager $envioManager,
        private FacturaManager $facturaManager,
        private SacaRepository $sacaRepository,
        private NomencladorRepository $nomencladorRepository,
        private FacturaRepository $facturaRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/recepcionar', name: 'procesar_factura', methods: ['GET'])]
    public function procesarFactura()
    {

        return $this->render('factura/recepcionar.html.twig',[]);
    }

    #[Route('/find-sacas-factura', name: 'find_sacas_factura', options: ["expose" => true] ,methods: ['POST'])]
    public function findSacasFactura(Request $request)
    {
        $noFactura = $request->get('noFactura');
        $sacas = $this->facturaRepository->findSacasNoFacturaAndEstado($noFactura);
        $envios = $this->facturaRepository->findEnviosNoFacturaAndEstado($noFactura);
        $anomalias = $this->nomencladorRepository->findByChildren('APP_SACA_ANOMALIA');
        $anomaliasE = $this->nomencladorRepository->findByChildren('APP_ENVIO_ANOMALIA');

        $html = $sacas || $envios ? $this->renderView('factura/sacas.html.twig', [
            'sacas'=>$sacas,
            'envios'=>$envios,
            'anomalias'=>$anomalias->toArray(),
            'anomaliasE'=>$anomaliasE->toArray(),
            'noFactura' => $noFactura]) : 'null';

        return new Response($html);
    }

    #[Route('/recepcionar-sacas-factura', name: 'recepcionar_sacas_factura', options: ["expose" => true] ,methods: ['POST'])]
    public function recepcionarSacasFactura(Request $request)
    {
        $noFactura = $request->get('noFactura');
        $sacas = $request->get('sacas') ?? [];
        $envios = $request->get('envios') ?? [];
        $todos = filter_var($request->get('todos'), FILTER_VALIDATE_BOOLEAN);
        $factura = $this->facturaRepository->getFacturaByNoFactura($noFactura);
        $estado = $this->nomencladorRepository->findOneByCodigo('APP_SACA_ESTADO_RECIBIDA');

        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->getUser();

        foreach ($sacas as $id)
        {
            $saca = $this->sacaRepository->find($id);
            $saca->setEstado($estado);

            $this->entityManager->persist($saca);
            $this->entityManager->flush();
        }

        foreach ($envios as $id)
        {
            $this->envioManager->cambiarEstado($id,$credencial);
        }

        if($todos)
        {
            $estado = $this->nomencladorRepository->findOneByCodigo('APP_FACTURA_ESTADO_RECIBIDA');
            $factura->setEstado($estado);
            $this->entityManager->persist($factura);
            $this->entityManager->flush();

            $this->facturaManager->createTraza($factura,$credencial);
        }
        return JsonResponse::fromJsonString('"Factura recibida correctamente"');
    }
}