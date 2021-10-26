<?php


namespace App\Controller;


use App\Repository\NomencladorRepository;
use App\Repository\SacaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/saca')]
class SacaController extends AbstractController
{

    public function __construct(
        private SacaRepository $sacaRepository,
        private NomencladorRepository $nomencladorRepository,
        private EntityManagerInterface $entityManager
    )
    {
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
}