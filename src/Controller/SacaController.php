<?php


namespace App\Controller;


use App\Repository\EnvioRepository;
use App\Repository\NomencladorRepository;
use App\Repository\SacaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/saca')]
class SacaController extends AbstractController
{

    public function __construct(
        private SacaRepository $sacaRepository,
        private NomencladorRepository $nomencladorRepository,
        private EnvioRepository $envioRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/aperturar', name: 'aperturar_saca', methods: ['GET'])]
    public function aperturarSaca()
    {
        return $this->render('saca/aperturar.html.twig',[]);
    }

    #[Route('/save-anomalia', name: 'saca_anomalia', options: ["expose" => true] ,methods: ['POST'])]
    public function saveSacaAnomalia(Request $request)
    {
        $id = $request->get('id');
        $anomalias = $request->get('anomalias');
        $saca = $this->sacaRepository->find($id);
        if (array_key_exists('DIFERENCIA DE PESO', $anomalias)){
            $actual = $saca->getPeso();
            $real = (float)$anomalias['DIFERENCIA DE PESO'];
            $diff = $real - $actual;

            $anomalias['DIFERENCIA DE PESO'] = [
                'peso real' => $real,
                'peso actual' => $actual,
                'diferencia' => $diff
            ];
        }
        $saca->setObservaciones($anomalias);
        $this->entityManager->persist($saca);
        $this->entityManager->flush();

        return JsonResponse::fromJsonString('"Anomalias agregadas correctamente"');
    }

    #[Route('/find-envios-saca', name: 'find_envios_saca', options: ["expose" => true] ,methods: ['POST'])]
    public function findEnviosSaca(Request $request)
    {
        $codTracking = $request->get('codTracking');
        $envios = $this->sacaRepository->findEnviosNoFacturaAndEstado($codTracking, 'APP_SACA_ESTADO_RECIBIDA');
        $anomaliasE = $this->nomencladorRepository->findByChildren('APP_ENVIO_ANOMALIA');

        $html = $envios ? $this->renderView('saca/envios.html.twig', [
            'envios'=>$envios,
            'anomaliasE'=>$anomaliasE->toArray(),
            'codTracking' => $codTracking]) : 'null';

        return new Response($html);
    }

    #[Route('/recepcionar-envios-saca', name: 'recepcionar_envios_saca', options: ["expose" => true] ,methods: ['POST'])]
    public function recepcionarEnviosSaca(Request $request)
    {
        $codTracking = $request->get('codTracking');
        $envios = $request->get('envios');
        $todos = filter_var($request->get('todos'), FILTER_VALIDATE_BOOLEAN);
        $saca = $this->sacaRepository->getSacaByCodigo($codTracking);
        $estado = $this->nomencladorRepository->findOneByCodigo('APP_ENVIO_ESTADO_RECEPCIONADO');

        foreach ($envios as $id)
        {
            $envio = $this->envioRepository->find($id);
            $envio->setEstado($estado);

            $this->entityManager->persist($envio);
            $this->entityManager->flush();
        }

        if($todos)
        {
            $estado = $this->nomencladorRepository->findOneByCodigo('APP_SACA_ESTADO_APERTURADA');
            $saca->setEstado($estado);
            $this->entityManager->persist($saca);
            $this->entityManager->flush();
        }
        return JsonResponse::fromJsonString('"Saca recibida correctamente"');
    }

}