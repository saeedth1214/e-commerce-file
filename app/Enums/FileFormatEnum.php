<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PSD()
 * @method static static EPS()
 */
final class FileFormatEnum extends Enum
{
    const EPS =   1;
    const AL =    2;
    const JPG =   3;
    const PNG =   4;
    const JPEG =  5;
    const PSD =   6;


    public static function asString(int $enumValue)
    {
        $key = static::getKey($enumValue);

        return strtolower($key);
    }
}
