<?php


namespace App\Entity\Envio;

use App\Entity\_Entity_;
use App\Enum\TipoFichero;
use App\Repository\FicheroEnvioAduanaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheroEnvioAduanaRepository::class)]
class FicheroEnvioAduana extends _Entity_
{
    #[ORM\Column(type: 'string', nullable: false)]
    private string $nombre_fichero;

    #[ORM\Column(type: 'datetime', length: 10, nullable: false)]
    private \DateTime $fecha_enviado;

    #[ORM\Column(type: 'datetime', length: 10, nullable: true)]
    private \DateTime $fecha_respuesta;

    #[ORM\Column(type: 'boolean')]
    private bool $es_correcto;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $tipo_fichero;

    /**
     * FicheroEnvioAduana constructor.
     * @param string $nombre_fichero
     * @param string $tipo_fichero
     */
    public function __construct(string $nombre_fichero, string $tipo_fichero)
    {
        $this->nombre_fichero = $nombre_fichero;
        $this->fecha_enviado = new \DateTime();
        $this->es_correcto = false;

        if (!in_array($tipo_fichero, TipoFichero::getAvailableTypes())) {
            throw new \InvalidArgumentException("Invalid type of file");
        }

        $this->tipo_fichero = $tipo_fichero;
    }


    /**
     * @return string
     */
    public function getNombreFichero(): string
    {
        return $this->nombre_fichero;
    }

    /**
     * @param string $nombre_fichero
     */
    public function setNombreFichero(string $nombre_fichero): void
    {
        $this->nombre_fichero = $nombre_fichero;
    }

    /**
     * @return \DateTime
     */
    public function getFechaEnviado(): \DateTime
    {
        return $this->fecha_enviado;
    }

    /**
     * @param \DateTime $fecha_enviado
     */
    public function setFechaEnviado(\DateTime $fecha_enviado): void
    {
        $this->fecha_enviado = $fecha_enviado;
    }

    /**
     * @return \DateTime
     */
    public function getFechaRespuesta(): \DateTime
    {
        return $this->fecha_respuesta;
    }

    /**
     * @param \DateTime $fecha_respuesta
     */
    public function setFechaRespuesta(\DateTime $fecha_respuesta): void
    {
        $this->fecha_respuesta = $fecha_respuesta;
    }

    /**
     * @return bool
     */
    public function isEsCorrecto(): bool
    {
        return $this->es_correcto;
    }

    /**
     * @param bool $es_correcto
     */
    public function setEsCorrecto(bool $es_correcto): void
    {
        $this->es_correcto = $es_correcto;
    }

    /**
     * @return string
     */
    public function getTipoFichero(): string
    {
        return $this->tipo_fichero;
    }

    /**
     * @param string $tipo_fichero
     */
    public function setTipoFichero(string $tipo_fichero): void
    {
        if (!in_array($tipo_fichero, TipoFichero::getAvailableTypes())) {
            throw new \InvalidArgumentException("Invalid type");
        }

        $this->tipo_fichero = $tipo_fichero;
    }

}