<?php

namespace App\Utils;

class SigloUtil
{
    private const SIGLO = 100; # Siglo son 100 Años
    const XIX = 19;
    const XX = 20;
    const XXI = 21;


    static function getTablaSiglo(int $siglo, bool $soloRango = false): array
    {
        $year = 1;
        $stop = false;
        $years = [];
        while ($stop == false) {
            # Obtener el siglo del año recorrido
            $sigloYear = (int)ceil($year / 100);

            # Comprobar el siglo que se esta buscando
            if ($sigloYear == $siglo) {
                # Si se necesita todos los años o solo el rango del siglo buscado
                if ($soloRango === true && count($years) === 0) {
                    $years[] = $year;
                } else if ($soloRango === false) {
                    $years[] = $year;
                }
            }

            # Comprobar si es mayor al siglo buscado
            if ($sigloYear > $siglo) {
                # Si se busca por rango agregar el año final
                if ($soloRango === true) $years[] = $year - 1;

                # Parar la busqueda
                $stop = true;
            }

            # Sumar un año más
            $year++;
        }

        return $years;
    }
}
