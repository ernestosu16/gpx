<?php


namespace App\Command\Envio;


use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\DTO\EntradaDespacho;
use App\DTO\Insertar;
use App\DTO\InsertarElementos;
use App\Entity\Envio\EnvioAduana;
use App\Entity\Estructura;
use App\Entity\Nomenclador;
use App\Enum\TipoFichero;
use App\Repository\Envio\EnvioAduanaRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class ProcesarEnviosAduanaCommand extends BaseCommand implements BaseCommandInterface
{
    private SymfonyStyle $io;
    private \Doctrine\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository|EnvioAduanaRepository $envioAduanaRepository;
    const ESTADO_RECEPCIONADO = 'APP_ENVIO_ESTADO_RECEPCIONADO';

    static function getCommandName(): string
    {
        return 'app:envio:recepcionado-aduana';
    }

    static function getCommandDescription(): string
    {
        return 'Procesar los envios recepcionados y enviiarlos al FTP Aduana';
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->envioAduanaRepository = $this->getRepository(EnvioAduana::class);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->getEntityManager();

        /** @var Estructura[] $estructurasTransitaria */
        $estructurasTransitaria = $em->getRepository(Estructura::class)->findEstructuraByTipo('EMPRESA');

        foreach ($estructurasTransitaria as $transitaria) {
            $envioAduanaResepcionado = $this->envioAduanaRepository->findEnvioAduanaByEstructuraAndEstado($transitaria, $this->getRepository(Nomenclador::class)->findOneByCodigo(self::ESTADO_RECEPCIONADO));

            $entradaDespacho = new EntradaDespacho();
            $entradaDespacho->setCodigoAduana($transitaria->getCodigoAduana());
            $entradaDespacho->setOperador($transitaria->getCodigoOperador());
            $incertarElementos = new InsertarElementos();
            $count = 0;

            foreach ($envioAduanaResepcionado as $envioAduana) {
                $incertarElementos->addInsertar($this->mapEnvioAdunaToInsertar($envioAduana));
                $count++;
                if ($count == 1000) {
                    $entradaDespacho->setInsertarElementos($incertarElementos);
                    $this->createAndSaveXML($entradaDespacho);

                    //limpio para crear un nuevo fichero
                    $incertarElementos = new InsertarElementos();
                    $count = 0;
                }
            }

            if ($count != 0) {
                $entradaDespacho->setInsertarElementos($incertarElementos);
                $this->createAndSaveXML($entradaDespacho);
            }
        }

        $this->io->success('Comando ejecutado satisfactoriamente.');

        return Command::SUCCESS;
    }

    public function mapEnvioAdunaToInsertar(EnvioAduana $envioAduana): Insertar
    {
        $envio = $envioAduana->getEnvio();
        $incertar = new Insertar($envio->getCodTracking(), $envio->getAgencia()->getDescripcion(), $envio->getProvincia()->getCodigoAduana(), $envio->getMunicipio()->getCodigoAduana(), $envio->getPaisOrigen()->getCodigoAduana(), $envio->getPeso());

        return $incertar;
    }

    public function createAndSaveXML(EntradaDespacho $entradaDespacho)
    {

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $xml_content = $serializer->serialize($entradaDespacho, 'xml', ['xml_format_output' => true, 'xml_root_node_name' => 'EntradaDespacho', 'encoder_ignored_node_types' => [
            \XML_PI_NODE, // removes XML declaration (the leading xml tag)
        ]]);

        $local_directory = $this->get('kernel')->getProjectDir() . '/public/download/envioAduana/paso1/';
        if (!file_exists($local_directory)) {
            mkdir($local_directory, 0777, true);
        }
        $file_name = date('Ymdhis') . 'paso1.xml';

        $stream = fopen($local_directory . $file_name, 'w+');
        fwrite($stream, $xml_content);
        fclose($stream);

        /**@var $emFichero \App\Manager\FicheroEnvioAduanaManager * */
        $emFichero = $this->getContainer()->get('app.manager.fichero_envio_aduana');

        $emFichero->createFicheroEnvioAduana($file_name, TipoFichero::TYPE_PASO1);
    }
}