<?php


namespace App\Enum;


abstract class TipoFichero
{
    const TYPE_PASO1 = "PASO1";
    const TYPE_PASO2 = "PASO2";

    /*protected static $typeName = [
        self::TYPE_PASO1 => 'PASO1',
        self::TYPE_PASO2 => 'PASO2',
    ];

    public static function getTypeName($type)
    {
        if (!isset(static::$typeName[$type])) {
            return "Unknown type ($type)";
        }

        return static::$typeName[$type];
    }*/

    public static function getAvailableTypes()
    {
        return [
            self::TYPE_PASO1,
            self::TYPE_PASO2,
        ];
    }
}