<?php

namespace App\Entity;

use App\Repository\PersonaRepository;
use App\Utils\RegexUtil;
use App\Utils\SigloUtil;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: PersonaRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_PERSONA', columns: ['hash'])]
#[ORM\Index(fields: ['numero_identidad'], name: 'IDX_NUMERO_IDENTIDAD')]
#[ORM\Index(fields: ['pais'], name: 'IDX_PAIS')]
class Persona extends _Entity_
{
    const HOMBRE = 'hombre';
    const MUJER = 'mujer';

    #[ORM\Column(type: 'string', length: 32, unique: true)]
    private string $hash;

    #[ORM\Column(type: 'string', length: 11, nullable: true)]
    #[Assert\Regex(pattern: RegexUtil::NUMERO_IDENTIDAD, message: 'Número de identidad es incorrecto')]
    #[Assert\Length(min: 11, max: 11)]
    #[SerializedName('id')]
    private ?string $numero_identidad;

    #[ORM\Column(type: 'string', length: 11, nullable: true)]
    private ?string $numero_pasaporte;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Regex(pattern: RegexUtil::TEXTO_ACENTO_SIN_ESPACIO, message: 'El nombre solo puede contener letras.')]
    #[SerializedName('primerNombre')]
    private string $nombre_primero;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    #[Assert\Regex(pattern: RegexUtil::TEXTO_ACENTO_SIN_ESPACIO, message: 'El nombre solo puede contener letras.')]
    #[SerializedName('segundoNombre')]
    private ?string $nombre_segundo;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Regex(pattern: RegexUtil::TEXTO_ACENTO_SIN_ESPACIO, message: 'El nombre solo puede contener letras.')]
    #[SerializedName('primerApellido')]
    private string $apellido_primero;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Regex(pattern: RegexUtil::TEXTO_ACENTO_SIN_ESPACIO, message: 'El nombre solo puede contener letras.')]
    #[SerializedName('segundoApellido')]
    private string $apellido_segundo;

    #[ORM\ManyToOne(targetEntity: Pais::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Pais $pais;

    #[ORM\Column(type: 'string', length: 20)]
    #[SerializedName('fechaNacimiento')]
    private ?string $fecha_nacimiento = '';

    #[Pure] public function __toString(): string
    {
        return $this->getNombreCompleto();
    }

    public function __construct()
    {
        $this->numero_pasaporte = null;
        $this->nombre_segundo = null;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getNumeroIdentidad(): ?string
    {
        return $this->numero_identidad;
    }

    public function setNumeroIdentidad(?string $numero_identidad): self
    {
        $this->numero_identidad = $numero_identidad;

        return $this;
    }

    public function getNumeroPasaporte(): ?string
    {
        return $this->numero_pasaporte;
    }

    public function setNumeroPasaporte(?string $numero_pasaporte): self
    {
        $this->numero_pasaporte = u($numero_pasaporte)->upper();

        return $this;
    }

    public function getNombrePrimero(): ?string
    {
        return $this->nombre_primero;
    }

    public function setNombrePrimero(string $nombre_primero): self
    {
        $this->nombre_primero = u($nombre_primero)->title();

        return $this;
    }

    public function getNombreSegundo(): ?string
    {
        return $this->nombre_segundo;
    }

    public function setNombreSegundo(?string $nombre_segundo): self
    {
        $this->nombre_segundo = u($nombre_segundo)->title();

        return $this;
    }

    public function getApellidoPrimero(): ?string
    {
        return $this->apellido_primero;
    }

    public function setApellidoPrimero(string $apellido_primero): self
    {
        $this->apellido_primero = u($apellido_primero)->title();

        return $this;
    }

    public function getApellidoSegundo(): ?string
    {
        return $this->apellido_segundo;
    }

    public function setApellidoSegundo(string $apellido_segundo): self
    {
        $this->apellido_segundo = u($apellido_segundo)->title();

        return $this;
    }

    public function getPais(): Pais
    {
        return $this->pais;
    }

    public function setPais(Pais $pais): Persona
    {
        $this->pais = $pais;

        return $this;
    }

    #[Pure] public function getNombre(): string
    {
        $n[] = $this->getNombrePrimero();
        if ($this->getNombreSegundo())
            $n[] = $this->getNombreSegundo();

        return implode(' ', $n);
    }

    #[Pure] public function getApellidos(): string
    {
        $a[] = $this->getApellidoPrimero();
        if ($this->getApellidoSegundo())
            $a[] = $this->getApellidoSegundo();

        return implode(' ', $a);
    }

    #[Pure] public function getNombreCompleto(): string
    {
        $c[] = $this->getNombre();
        if ($this->getApellidos())
            $c[] = $this->getApellidos();

        return implode(' ', $c);
    }

    /**
     * @return string
     */
    public function getFechaNacimiento(): string
    {
        return $this->fecha_nacimiento;
    }

    /**
     * @param string $fecha_nacimiento
     */
    public function setFechaNacimiento(string $fecha_nacimiento): void
    {
        $this->fecha_nacimiento = $fecha_nacimiento;
    }

    public function getSiglo(): int
    {
        $numeroSiglo = (int)u($this->getNumeroIdentidad())->slice(6, -4)->toString();

        if (0 <= $numeroSiglo && $numeroSiglo <= 5)
            return SigloUtil::XX;

        if (6 <= $numeroSiglo && $numeroSiglo <= 8)
            return SigloUtil::XXI;

        return SigloUtil::XIX;
    }

    public function getNacimiento(): DateTime
    {
        $longevidadMaxima = 119;
        $nacimiento = null;
        $dateNow = new DateTime('now');
        $numeroNacimiento = u($this->getNumeroIdentidad())->slice(0, -5)->chunk(2);

        $years = SigloUtil::getTablaSiglo($this->getSiglo(), true);
        foreach ($years as $year) {
            $fechaToString = u($year)
                ->slice(0, -2)->append($numeroNacimiento[0])
                ->append('-')->append($numeroNacimiento[1])
                ->append('-')->append($numeroNacimiento[2])
                ->toString();
            $fecha = new DateTime($fechaToString);

            $interval = $dateNow->diff($fecha);

            # Comprobar que la fecha no rebase a la fecha actual
            # y que los annos no superen al tiempo maximo de vida registrado
            if ($fecha > $dateNow || $interval->y > $longevidadMaxima)
                continue;

            $nacimiento = new DateTime($fechaToString);

        }

        return $nacimiento;
    }

    public function getSexo(): string
    {
        $numeroSexo = (int)u($this->getNumeroIdentidad())->slice(9, -1)->toString();

        $sexo = ($numeroSexo % 2) ? self::MUJER : self::HOMBRE;
        return u($sexo)->title()->toString();
    }
}
