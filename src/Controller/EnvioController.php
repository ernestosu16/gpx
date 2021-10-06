<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use App\Entity\Persona;
use App\Form\EnvioType;
use App\Manager\EnvioManager;
use App\Repository\EnvioManifiestoRepository;
use App\Repository\EnvioRepository;
use App\Repository\LocalizacionRepository;
use App\Utils\MyResponse;
use JMS\Serializer\SerializerBuilder;
use phpDocumentor\Reflection\Types\False_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;
use function Sodium\add;

#[Route('/envio')]
class EnvioController extends AbstractController
{
    public function __construct(
        private LocalizacionRepository $localizacion,
        private EnvioManifiestoRepository $envioManifiesto,
        private EnvioManager $envioManager
    )
    {
    }

    #[Route('/', name: 'envio_index', methods: ['GET'])]
    public function index(Request $request): Response
    {

        if ($request->isXmlHttpRequest()){

            //Obtener array de envios a recepcionar
            $datos = $request->request->get['data'];

            $miRespuesta = new MyResponse();

            if ($this->envioManager->recepcionarEnvios($datos)){

                $miRespuesta->setEstado(true);
                $miRespuesta->setData(null);
                $miRespuesta->setMensaje("OK");

            }else{

                $miRespuesta->setEstado(false);
                $miRespuesta->setData(null);
                $miRespuesta->setMensaje("Error al recepcionar los envios correspondientes");

            }

            $serializer = SerializerBuilder::create()->build();
            $miRespuestaJson = $serializer->serialize($miRespuesta,"json");

            return JsonResponse::fromJsonString($miRespuestaJson);

        }else {


            $provincias = $this->localizacion->findAllProvincia();

            $municipios = $this->localizacion->findByTipoCodigo(LocalizacionTipo::MUNICIPIO);

            $anomalias = ["envio faltante",
                "envio mal encaminado",
                "envio mojado",
                "envio roto",
                "acceso al contenido",
                "envio con precinta",
                "envio violado",
                "envio de entidad, bajo control aduana postal",
                "envio no controlado",
                "notificado a",
                "cambio tipo de producto",
                "cambio sub tipo de producto",
                "generado aviso",
                "eliminado aviso",
                "envio no manifestado",
                "envio manifestado en otro manifiesto",
                "diferencia de peso"];

            $nacionalidades = ['AFGANISTAN', 'ALBANIA', 'ALEMANIA', 'ANDORRA', 'ANGOLA', 'ANGUILA', 'ANTIGUA Y BARBUDA', 'ANTILLAS NEERLANDESAS', 'ARABIA SAUDITA', 'ARGELIA', 'ARGENTINA', 'BURUNDI', 'CAMBODIA', 'CAMBOYA', 'CAMERUN', 'CANADA', 'CHAD', 'CHILE', 'CHINA', 'CHIPRE', 'COLOMBIA', 'CONGO', 'COSTA DE MARFIL', 'COSTA RICA', 'CROACIA', 'DINAMARCA', 'DOMINICA'];

            $curries = ['CUBA ENVIO', 'ALL COSUMER', 'CUGRANCA', 'IBT', 'BORDOY', 'APCARGO', 'DORMAR', 'LOBATON', 'GRAN CASTOR', 'CARIBBEAN LOGISTIC', 'CORRESPONDENCIA EXPRESS', 'MARFEENTERPRISE. SA'];

            dump('hola dump');
            dump($provincias);

            //dump($this->localizacion->createQueryBuilderMunicipio());


            return $this->render('envio/recepcionarEnvio.html.twig', [
                "anomalias" => $anomalias,
                "provincias" => $provincias,
                "nacionalidades" => $nacionalidades,
                "curries" => $curries,
                "municipios" => $municipios
            ]);

        }
    }

    /*public function obtenerEnvioManifestado(string $codTracking){

        $envioManifestado = $this->envioManifestado->findOneBySomeField($codTracking);

        if ($envioManifestado){
            return $envioManifestado;
        }else{
            return false;
        }

    }*/



    /*#[Route('/', name: 'envio_index', methods: ['GET'])]
    public function index(EnvioRepository $envioRepository): Response
    {
        return $this->render('envio/index.html.twig', [
            'envios' => $envioRepository->findAll(),
        ]);
    }*/

    #[Route('/new', name: 'envio_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $envio = new Envio();
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($envio);
            $entityManager->flush();

            return $this->redirectToRoute('envio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('envio/new.html.twig', [
            'envio' => $envio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'envio_show', methods: ['GET'])]
    public function show(Envio $envio): Response
    {
        return $this->render('envio/show.html.twig', [
            'envio' => $envio,
        ]);
    }

    #[Route('/{id}/edit', name: 'envio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Envio $envio): Response
    {
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('envio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('envio/edit.html.twig', [
            'envio' => $envio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'envio_delete', methods: ['POST'])]
    public function delete(Request $request, Envio $envio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$envio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($envio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('envio_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/envio/buscar-envio-manifestado', name: 'envio_manifestado', options: ["expose" => true] , methods: ['POST'])]
    public function buscarEnvioManifestado(Request $request){

        if ($request->isXmlHttpRequest()){

            $guia = $request->request->get('noGuia');
            $tracking = $request->request->get('codTracking');

            $envioManifestadoService = $this->envioManager->obtnerEnvioManifestado($guia,$tracking);

            dump('envio $envioManifestadoService');
            dump($envioManifestadoService);

            $miRespuesta = new MyResponse();

            //Si no existe el envio
            if ( ! $envioManifestadoService ){

                $miRespuesta->setEstado(false);
                $miRespuesta->setData(null);
                $miRespuesta->setMensaje("No existe el envio en la guia solicitada");

                //Si existe pero es interes de aduana
            }else if($envioManifestadoService->isEntidadCtrlAduana()){

                $miRespuesta->setEstado(false);
                $miRespuesta->setData(null);
                $miRespuesta->setMensaje("El envio solicitado es interés de aduana, no se puede recepcionar.");

                //Si existe y esta correcto
            }else{

                $miRespuesta->setEstado(true);
                $miRespuesta->setData($envioManifestadoService);
                $miRespuesta->setMensaje("OK");

            }

            $serializer = SerializerBuilder::create()->build();
            $miRespuestaJson = $serializer->serialize($miRespuesta,"json");

            return JsonResponse::fromJsonString($miRespuestaJson);

        }else{
            dump("Hacker");
            throwException('Hacker');
        }

    }
}
