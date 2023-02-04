<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PSD()
 * @method static static EPS()
 */
final class FileFormatEnum extends Enum
{
    const EPS =   0;
    const PSD =   1;


    public static function asString(int $enumValue)
    {
        $key = static::getKey($enumValue);

        return strtolower($key);
    }
}
