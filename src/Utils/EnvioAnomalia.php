<?php


namespace App\Utils;


class EnvioAnomalia
{
    private $id_anomalia;
    private $nombre_anomalia;
    private $descripcion_anomalia_envio;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIdAnomalia()
    {
        return $this->id_anomalia;
    }

    /**
     * @param mixed $id_anomalia
     */
    public function setIdAnomalia($id_anomalia): void
    {
        $this->id_anomalia = $id_anomalia;
    }

    /**
     * @return mixed
     */
    public function getNombreAnomalia()
    {
        return $this->nombre_anomalia;
    }

    /**
     * @param mixed $nombre_anomalia
     */
    public function setNombreAnomalia($nombre_anomalia): void
    {
        $this->nombre_anomalia = $nombre_anomalia;
    }

    /**
     * @return mixed
     */
    public function getDescripcionAnomaliaEnvio()
    {
        return $this->descripcion_anomalia_envio;
    }

    /**
     * @param mixed $descripcion_anomalia_envio
     */
    public function setDescripcionAnomaliaEnvio($descripcion_anomalia_envio): void
    {
        $this->descripcion_anomalia_envio = $descripcion_anomalia_envio;
    }

}