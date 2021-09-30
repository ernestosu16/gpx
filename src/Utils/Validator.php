<?php

namespace App\Utils;

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
}
