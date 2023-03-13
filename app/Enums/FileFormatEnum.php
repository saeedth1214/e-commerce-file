<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static EPS()
 * @method static AL()
 * @method static JPG()
 * @method static PNG()
 * @method static JPEG()
 * @method static ZIP()
 */
final class FileFormatEnum extends Enum
{
    const EPS =   1;
    const AL =    2;
    const JPG =   3;
    const PNG =   4;
    const JPEG =  5;
    const PSD =   6;
    const ZIP =   7;


    public static function asString(int $enumValue)
    {
        $key = static::getKey($enumValue);

        return strtolower($key);
    }
}
