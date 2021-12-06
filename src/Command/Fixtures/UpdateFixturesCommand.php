<?php

namespace App\Command\Fixtures;

use App\Command\BaseCommand;
use App\Command\BaseCommandInterface;
use App\Entity\Localizacion;
use App\Entity\Nomenclador\LocalizacionTipo;
use App\Entity\Pais;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use function Symfony\Component\String\u;

final class UpdateFixturesCommand extends BaseCommand implements BaseCommandInterface
{
    static function getCommandName(): string
    {
        return 'app:fixtures:update';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updatePais();
        $this->updateLocalizacion();
        return Command::SUCCESS;
    }


    private function updateLocalizacion()
    {
        $tipo = $this->getRepository(LocalizacionTipo::class)->findOneByCodigo(LocalizacionTipo::PROVINCIA);

        if (!$tipo)
            throw new InvalidOptionException(LocalizacionTipo::PROVINCIA);

        $collection = $this->getRepository(Localizacion::class)->findByTipo($tipo);

        $lista = ['provincias' => [], 'municipios' => []];
        foreach ($collection as $provincia) {
            $lista['provincias'][] = [
                'codigo' => $provincia->getCodigo(),
                'nombre' => $provincia->getNombre(),
                'descripcion' => $provincia->getDescripcion(),
                'codigo_aduana' => $provincia->getCodigoAduana(),
            ];
            $lista['municipios'][$provincia->getCodigo()] = [];
            foreach ($provincia->getChildren() as $municipio) {
                $lista['municipios'][$provincia->getCodigo()][] = [
                    'codigo' => $municipio->getCodigo(),
                    'nombre' => $municipio->getNombre(),
                    'codigo_aduana' => $municipio->getCodigoAduana(),
                ];
            }
        }

        if (empty($lista['provincias']) && !empty($lista['municipios']))
            return;

        file_put_contents('src/Config/Fixtures/localizacion.yaml', Yaml::dump($lista));
    }

    private function updatePais()
    {
        $collection = $this->getRepository(Pais::class)->findAll();

        $lista = ['pais' => []];
        foreach ($collection as $item) {
            $lista['pais'][] = [
                'nombre' => u($item->getNombre())->title()->toString(),
                'iata' => $item->getIata(),
                'codigo_aduana' => $item->getCodigoAduana(),
            ];
        }

        if (empty($lista['pais']))
            return;

        file_put_contents('src/Config/Fixtures/pais.yaml', Yaml::dump($lista));
    }
}
