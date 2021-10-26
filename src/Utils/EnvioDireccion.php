<?php


namespace App\Utils;


use App\Entity\Localizacion;
use JMS\Serializer\Annotation\SerializedName;

class EnvioDireccion
{
    #[SerializedName('calle')]
    private string $calle;

    #[SerializedName('entre_calle')]
    private string $entre_calle;

    #[SerializedName('y_calle')]
    private string $y_calle;

    #[SerializedName('numero')]
    private string $numero;

    #[SerializedName('piso')]
    private string $piso;

    #[SerializedName('apto')]
    private string $apto;

    #[SerializedName('provincia')]
    private string $provincia;

    #[SerializedName('municipio')]
    private string $municipio;


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
     * @return string
     */
    public function getProvincia(): string
    {
        return $this->provincia;
    }

    /**
     * @param string $provincia
     */
    public function setProvincia(string $provincia): void
    {
        $this->provincia = $provincia;
    }

    /**
     * @return string
     */
    public function getMunicipio(): string
    {
        return $this->municipio;
    }

    /**
     * @param string $municipio
     */
    public function setMunicipio(string $municipio): void
    {
        $this->municipio = $municipio;
    }

}