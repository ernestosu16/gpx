<?php

namespace App\Command\Configurar;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Entity\Localizacion;
use App\Entity\LocalizacionTipo;
use App\Repository\LocalizacionRepository;
use App\Repository\LocalizacionTipoRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class LocalizacionCommand extends BaseCommand implements BaseCommandInterface
{
    static function getCommandName(): string
    {
        return 'app:configurar:localizacion';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException|OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = Yaml::parseFile($this->getKernel()->getProjectDir() . '/src/Config/Fixtures/localizacion.yaml');

        if ($data)
            $this->procesando($data);

        return Command::SUCCESS;
    }

    /**
     * @throws ORMException|OptimisticLockException
     */
    private function procesando(array $data)
    {
        $em = $this->getEntityManager();

        $em->beginTransaction();

        /** @var LocalizacionRepository $localizacionRepository */
        $localizacionRepository = $em->getRepository(Localizacion::class);

        /** @var LocalizacionTipoRepository $tipoRepository */
        $tipoRepository = $em->getRepository(LocalizacionTipo::class);
        foreach ($data['provincias'] as $provincia) {
            if ($localizacionRepository->findOneByCodigo($provincia['codigo']))
                continue;

            $entity = new Localizacion();
            $entity->setNombre($provincia['nombre']);
            $entity->setDescripcion($provincia['descripcion']);
            $entity->setCodigo($provincia['codigo']);
            $entity->setTipo($tipoRepository->getTipoProvincia());

            $em->persist($entity);
            $em->flush();

            foreach ($data['municipios'][$provincia['codigo']] as $municipio) {
                $provinciaEntity = $localizacionRepository->findOneByCodigo($provincia['codigo']);

                if (!$provinciaEntity)
                    throw new ORMException('No se encontró el código "' . $provincia['codigo'] . '"');

                if ($localizacionRepository->findOneByCodigo($municipio['codigo']))
                    continue;

                $entity = new Localizacion();
                $entity->setParent($provinciaEntity);
                $entity->setNombre($municipio['nombre']);
                $entity->setCodigo($municipio['codigo']);
                $entity->setTipo($tipoRepository->getTipoMunicipio());

                $em->persist($entity);
            }
            $em->flush();
        }

        $em->commit();
    }
}
