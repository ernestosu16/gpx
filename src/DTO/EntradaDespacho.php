<?php


namespace App\DOM;


use JMS\Serializer\Annotation\SerializedName;

class EntradaDespacho
{
    #[SerializedName('operador')]
    private string $operador;

    #[SerializedName('codigoAduana')]
    private string $codigo_aduana;

    #[SerializedName('insertarElementos')]
    private InsertarElementos $insertar_elementos;


    /**
     * @return string
     */
    public function getOperador(): string
    {
        return $this->operador;
    }

    /**
     * @param string $operador
     */
    public function setOperador(string $operador): void
    {
        $this->operador = $operador;
    }

    /**
     * @return string
     */
    public function getCodigoAduana(): string
    {
        return $this->codigo_aduana;
    }

    /**
     * @param string $codigo_aduana
     */
    public function setCodigoAduana(string $codigo_aduana): void
    {
        $this->codigo_aduana = $codigo_aduana;
    }

    /**
     * @return InsertarElementos
     */
    public function getInsertarElementos(): InsertarElementos
    {
        return $this->insertar_elementos;
    }

    /**
     * @param InsertarElementos $insertar_elementos
     */
    public function setInsertarElementos(InsertarElementos $insertar_elementos): void
    {
        $this->insertar_elementos = $insertar_elementos;
    }


}