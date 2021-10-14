<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\EnvioManifiesto;
use App\Entity\Estructura;
use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use App\Entity\Persona;
use App\Entity\TrabajadorCredencial;
use App\Form\EnvioType;
use App\Manager\EnvioManager;
use App\Repository\AgenciaRepository;
use App\Repository\EnvioManifiestoRepository;
use App\Repository\EnvioRepository;
use App\Repository\LocalizacionRepository;
use App\Repository\NomencladorRepository;
use App\Repository\PaisRepository;
use App\Utils\EnvioPreRecepcion;
use App\Utils\MyResponse;
use JMS\Serializer\SerializerBuilder;
use phpDocumentor\Reflection\Types\False_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use function PHPUnit\Framework\throwException;
use function Sodium\add;

#[Route('/envio')]
class EnvioController extends AbstractController
{
    public function __construct(
        private LocalizacionRepository $localizacion,
        private EnvioManifiestoRepository $envioManifiesto,
        private EnvioManager $envioManager,
        private PaisRepository $paisRepository,
        private AgenciaRepository $agenciaRepository,
        private NomencladorRepository $nomencladorRepository
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

            $anomalias = $this->nomencladorRepository->findByChildren('APP_ENVIO_ANOMALIA');

            $nacionalidades = $this->paisRepository->findAll();

            $curries = $this->agenciaRepository->findAll();

            //dump($this->localizacion->createQueryBuilderMunicipio());


            return $this->render('envio/recepcionarEnvio.html.twig', [
                "anomalias" => $anomalias->toArray(),
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

            $miRespuesta = new MyResponse();

            //Si no existe el envio
            if ( ! $envioManifestadoService ){

                $miRespuesta->setEstado(false);
                $miRespuesta->setData(null);
                $miRespuesta->setMensaje("No existe el envio en la guia solicitada");

                //Si existe pero es interes de aduana
            }else if($envioManifestadoService->entidad_ctrl_aduana){

                $miRespuesta->setEstado(true);
                $miRespuesta->setData($envioManifestadoService);
                $miRespuesta->setMensaje("El envio solicitado es interés de aduana.");

                //Si existe y esta correcto
            }else{

                $miRespuesta->setEstado(true);
                $miRespuesta->setData($envioManifestadoService);
                $miRespuesta->setMensaje("Envio buscado con exito");

            }

            $serializer = SerializerBuilder::create()->build();
            $miRespuestaJson = $serializer->serialize($miRespuesta,"json");

            return JsonResponse::fromJsonString($miRespuestaJson);

        }else{
            dump("Hacker");
            throwException('Hacker');
        }

    }

    #[Route('/envio/recepcionar-envios', name: 'recepcionar_envios', options: ["expose" => true] , methods: ['POST'])]
    public function recepcionarEnvios(Request $request){

        if ($request->isXmlHttpRequest()){

            $envios = $request->request->get('envios');

            /** @var TrabajadorCredencial $credencial */
            $credencial = $this->getUser();

            $result = $this->envioManager->recepcionarEnvios($envios,$credencial);

            $enviosRecepcionados= $this->envioManager->recepcionarEnvios([]);

            $miRespuesta = new MyResponse();

            //Si dio algun error par aguardar los envios
            if ( ! $enviosRecepcionados ){

                $miRespuesta->setEstado(false);
                $miRespuesta->setData(null);
                $miRespuesta->setMensaje("Se ha producido un error durante la salva de los envios");


            }else{

                $miRespuesta->setEstado(true);
                $miRespuesta->setData('');
                $miRespuesta->setMensaje("Envios recepcionados correctamente !!!");

            }

            $serializer = SerializerBuilder::create()->build();
            $miRespuestaJson = $serializer->serialize($miRespuesta,"json");

            return JsonResponse::fromJsonString($miRespuestaJson);

        }else{
            dump("Hacker");
            throwException('Hacker');
        }

    }

    #[Route('/envio/buscar-municipio', name: 'buscar_municipio', options: ["expose" => true] , methods: ['POST'])]
    public function municipioDeUnaProvincia(Request $request){

        if ($request->isXmlHttpRequest()){

            $provincia = $request->request->get('provincia');

            $municipios = $this->localizacion->findMunicipiosOfProvinciaById($provincia);

            $miRespuesta = new MyResponse();

            //Si no existen municipios
            if ( ! $municipios ){

                $miRespuesta->setEstado(false);
                $miRespuesta->setData(null);
                $miRespuesta->setMensaje("No existe el envio en la guia solicitada");

                //Si existe pero es interes de aduana
            }else{

                $miRespuesta->setEstado(true);
                $miRespuesta->setData($municipios);
                $miRespuesta->setMensaje("Municipios buscados con exito");

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
