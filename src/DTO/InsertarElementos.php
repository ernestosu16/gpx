<?php


namespace App\DTO;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\SerializedName;

class InsertarElementos
{
    /** @var array|Insertar[] $insertar **/
    #[SerializedName('insertar')]
    private array $insertar;

    /**
     * InsertarElementos constructor.
     */
    public function __construct()
    {
        $this->insertar = array();
    }

    /**
     * @param Insertar $insertar
     */
    public function addInsertar(Insertar $insertar): void
    {
        $this->insertar[] = $insertar;
    }

    /**
     * @return Insertar[]|array
     */
    public function getInsertar(): array
    {
        return $this->insertar;
    }


}