<?php

namespace App\Utils;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use function Symfony\Component\String\u;

class Validator
{
    public function validateUsername(?string $username): string
    {
        if (empty($username))
            throw new InvalidArgumentException('El nombre de usuario no puede estar vacío.');

        if (1 !== preg_match('/^[a-z_]+$/', $username))
            throw new InvalidArgumentException('El nombre de usuario debe contener solo caracteres latinos en minúscula y guiones bajos.');

        return $username;
    }

    public function validatePassword(?string $plainPassword): string
    {
        if (empty($plainPassword))
            throw new InvalidArgumentException('La contraseña no puede estar vacía.');

        if (u($plainPassword)->trim()->length() < 6)
            throw new InvalidArgumentException('La contraseña debe tener al menos 6 caracteres.');

        return $plainPassword;
    }

    public function validateEmail(?string $email): string
    {
        if (empty($email))
            throw new InvalidArgumentException('El correo electrónico no puede estar vacío.');

        if (null === u($email)->indexOf('@'))
            throw new InvalidArgumentException('El correo electrónico debe verse como un correo electrónico real.');

        return $email;
    }

    public function validateFullName(?string $fullName): string
    {
        if (empty($fullName))
            throw new InvalidArgumentException('El nombre completo no puede estar vacío.');

        return $fullName;
    }

    public function validatePalabra(?string $texto): string
    {
        if (empty($texto))
            throw new InvalidArgumentException('El campo no puede estar vacío.');

        if (1 !== preg_match(RegexUtil::TEXTO_ACENTO_SIN_ESPACIO, $texto))
            throw new InvalidArgumentException('El campo es incorrecto, solo puede tener un texto');

        return $texto;
    }

    public function validateNumeroIdentidad(?string $numeroIdentidad): string
    {
        if (empty($numeroIdentidad))
            throw new InvalidArgumentException('El número de identidad no puede estar vacío.');

        if (1 !== preg_match(RegexUtil::NUMERO_IDENTIDAD, $numeroIdentidad))
            throw new InvalidArgumentException('El número de identidad es incorrecto.');

        return $numeroIdentidad;
    }

    public static function validarCI(string $ci): array
    {
        $errores_ci = "Destinatario con error en el CI ". $ci ."  \r\n";
        $arreglo = array(
            'valid' => true,
            'error' => ''
        );

        if (strlen($ci) != 11) {
            $errores_ci .= "-Formato incorrecto del carne de identidad.(No tiene 11 dígitos)  \r\n";
            $arreglo['valid'] = false;
            $arreglo['error'] = $errores_ci;
            return $arreglo;
        } else {
            //validando el MES en el carnet de identidad
            if (substr($ci,2,2) > 12 || substr($ci,2,2) <= 0)
            {
                $errores_ci .= "-Formato incorrecto del carne de identidad.(Mes incorrecto) \r\n";
                $arreglo['valid'] = false;
                $arreglo['error'] = $errores_ci;
            }
            //validando el DÍA en el carnet de identidad
            if (substr($ci,4,2) > 31 || substr($ci,4,2) <= 0)
            {
                $errores_ci .= "-Formato incorrecto del carne de identidad.(Dia incorrecto) \r\n";
                $arreglo['valid'] = false;
                $arreglo['error'] = $errores_ci;
            }
            //validando que sea SOLO NÚMERO en el carnet de identidad
            if(preg_match('/^([0-9])*$/', $ci) == 0)
            {
                $errores_ci .= "-Formato incorrecto del carne de identidad.(Este contiene letras) \r\n";
                $arreglo['valid'] = false;
                $arreglo['error'] = $errores_ci;
            }
            //validando Febrero en el carnet de identidad
            if (substr($ci,2,2) == 02)
            {
                if(substr($ci,4,2) > 29)
                {
                    $errores_ci .= "-Formato incorrecto del carne de identidad.(Día incorrecto, en febrero el día no puede ser mayor a 29) \r\n";
                    $arreglo['valid'] = false;
                    $arreglo['error'] = $errores_ci;
                }
            }
        }
        return $arreglo;
    }

    public static function validarFecha(string $fecha): bool
    {
        $fecha_now = date('Y-m-d');

        $anno_now = substr($fecha_now, 0, 4);
        $mes_now = substr($fecha_now, 5, 2);
        $dia_now = substr($fecha_now, 8, 2);

        $anno = substr($fecha, 0, 4);
        $mes = substr($fecha, 5, 2);
        $dia = substr($fecha, 8, 2);

        $anno_actual = date('Y');

        if (strlen($fecha) != 10)
        {
            return false;
        }
        if ($anno > $anno_actual)
        {
            return false;
        }
        if ($anno == $anno_now)
        {
            if ($mes <= $mes_now)
            {
                if ($dia > $dia_now -1)
                {
                    return false;
                }
            } else {
                return false;
            }
        }
        if ($anno < 1900) {
            return false;
        } else {
            if ($mes > 12 || $mes == 0)
            {
                return false;
            }
            if ($dia > 31 || $dia == 0)
            {
                return false;
            }
            return true;
        }
    }

    public static function tieneCaracteresEspeciales(string $cadena): bool
    {
        $caracteres_especiales = array('"', "¨", "º", "~",
            "#", "@", "|", "!", "·", "$", "%", "&", "/",
            "(", ")", "?", "¡", ".", "¿", "[", "^", "]",
            "+", "}", "{", "¨", "´", ">", "< ", ";", ",", ":",
            'ç', 'Ç', 'à', 'ä', 'â', 'ª', 'À', 'Â', 'Ä', 'è', 'ë', 'ê',
            'È', 'Ê', 'Ë', 'ì', 'ï', 'î', 'Ì', 'Ï', 'Î', 'ò', 'ö', 'ô',
            'Ò', 'Ö', 'Ô', 'ù', 'ü', 'û', 'Ù', 'Û', 'Ü');

        $cont = 0;
        foreach ($caracteres_especiales as $findme)
        {
            $pos = strpos($cadena, $findme);

            if ($pos !== false) {
                $cont++;
            }
        }

        if($cont > 0) return true;

        return false;
    }
}
