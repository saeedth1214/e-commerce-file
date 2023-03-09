<?php

namespace App\Enums;

use App\Contracts\LocalizeFaDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static  MONTHLY()
 * @method static  QUARTELY()
 * @method static  BIANNUAL()
 */
final class PlanTypeEnum extends Enum implements LocalizedEnum, LocalizeFaDescription
{
    const MONTHLY = 1;
    const QUARTELY = 2;
    const BIANNUAL = 3;

    private static array $incrementDays = [
        1 => 30,
        2 => 90,
        3 => 180,
    ];

    public static function getLocalizeFaDescription(): array
    {
        return [
            static::class => [
                'MONTHLY' => '1 ماهه - 30 روز',
                'QUARTELY' => '3 ماهه - 90 روز ',
                'BIANNUAL' => '6 ماهه - 180روز ',
            ]
        ];
    }
    public static function convertToDays(int $key): int
    {
        return static::$incrementDays[$key];
    }
}
