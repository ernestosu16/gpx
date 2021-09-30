<?php

namespace App\Utils;

final class RegexUtil
{
    const CODIGO = '/^[a-zA-Z_]+$/';
    const CODIGO_REEUP = '/^[\d]{3}\.[\d]{1}\.[\d]{5}$/';
    const CODIGO_NIT = '/^[0-9]+$/';
    const TEXTO_ACENTO_ESPACIO = '/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/';
    const TEXTO_ACENTO_SIN_ESPACIO = '/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+$/';
    const SOLO_NUMERO = '/^[0-9]+$/';
    const NUMERO_IDENTIDAD = '/^\d{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])\d{5}$/';
    const TELEFONO = '/^[0-9]{8,8}$/';
}
