<?php

namespace App\Enums;

use App\Contracts\LocalizeFaDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static PENDING()
 * @method static PAY_OK()
 * @method static PAY_FAILED()
 */
final class OrderTypeEnum extends Enum implements LocalizedEnum, LocalizeFaDescription
{
    const PENDING =   1;
    const PAY_OK =   2;
    const PAY_FAILED = 3;


    public static function getLocalizeFaDescription(): array
    {

        return [
            self::class => [
                'PENDING' => 'در انتظار پرداخت',
                'PAY_OK' => 'پرداخت شده',
                'PAY_FAILED' => 'ناموفق',
            ]
        ];
    }
}
