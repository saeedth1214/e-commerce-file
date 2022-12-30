<?php

namespace App\Enums;

use App\Contracts\LocalizeFaDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static ACTIVE()
 * @method static static INACTIVE()
 * @method static static EXPIRED()
 */
final class PlanStatusEnum extends Enum implements LocalizedEnum, LocalizeFaDescription
{
    const ACTIVE = 1;
    const INACTIVE = 2;
    const EXPIRED = 3;



    public static function getLocalizeFaDescription(): array
    {
        return [
            static::class => [
                'ACTIVE' => 'فعال',
                'INACTIVE' => 'غیرفعال',
                'EXPIRED' => 'منقضی شده',
            ]
        ];
    }
}
