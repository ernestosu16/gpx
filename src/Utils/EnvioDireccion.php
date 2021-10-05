<?php


namespace App\Util;


use App\Entity\Localizacion;

class EnvioDireccion
{

    private string $calle;

    private string $entre_calle;

    private string $y_calle;

    private string $numero;

    private string $piso;

    private string $apto;

    private Localizacion $municipio;


    /**
     * @return string
     */
    public function getCalle(): string
    {
        return $this->calle;
    }

    /**
     * @param string $calle
     */
    public function setCalle(string $calle): void
    {
        $this->calle = $calle;
    }

    /**
     * @return string
     */
    public function getEntreCalle(): string
    {
        return $this->entre_calle;
    }

    /**
     * @param string $entre_calle
     */
    public function setEntreCalle(string $entre_calle): void
    {
        $this->entre_calle = $entre_calle;
    }

    /**
     * @return string
     */
    public function getYCalle(): string
    {
        return $this->y_calle;
    }

    /**
     * @param string $y_calle
     */
    public function setYCalle(string $y_calle): void
    {
        $this->y_calle = $y_calle;
    }

    /**
     * @return string
     */
    public function getNumero(): string
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     */
    public function setNumero(string $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return string
     */
    public function getPiso(): string
    {
        return $this->piso;
    }

    /**
     * @param string $piso
     */
    public function setPiso(string $piso): void
    {
        $this->piso = $piso;
    }

    /**
     * @return string
     */
    public function getApto(): string
    {
        return $this->apto;
    }

    /**
     * @param string $apto
     */
    public function setApto(string $apto): void
    {
        $this->apto = $apto;
    }

    /**
     * @return Localizacion
     */
    public function getMunicipio(): Localizacion
    {
        return $this->municipio;
    }

    /**
     * @param Localizacion $municipio
     */
    public function setMunicipio(Localizacion $municipio): void
    {
        $this->municipio = $municipio;
    }

    /**
     * @return string
     */
    public function getMunicipioNombre(): string
    {
        return $this->municipio->getNombre();
    }

    /**
     * @return string
     */
    public function getMunicipioCodigo(): string
    {
        return $this->municipio->getCodigo();
    }


}