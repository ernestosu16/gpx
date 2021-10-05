<?php


namespace App\Utils;


class EnvioAnomalia
{
    private $anomalia;
    private $descripcion;

    /**
     * @return mixed
     */
    public function getAnomalia()
    {
        return $this->anomalia;
    }

    /**
     * @param mixed $anomalia
     */
    public function setAnomalia($anomalia): void
    {
        $this->anomalia = $anomalia;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion): void
    {
        $this->descripcion = $descripcion;
    }





}