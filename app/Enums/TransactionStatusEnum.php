<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 18:26 PM
 */

namespace App\Enums;

use App\Contracts\LocalizeFaDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static Paying()
 * @method static Payed()
 * @method static Canceled()
 */
final class TransactionStatusEnum extends Enum implements LocalizedEnum, LocalizeFaDescription
{
    const Paying = 1;
    const Payed = 2;
    const Canceled = 3;


    public static function getLocalizeFaDescription(): array
    {
        return [
            static::class => [
                'Paying' => 'در حال پرداخت',
                'Payed' => 'پرداخت شده',
                'Canceled' => 'پرداخت ناموفق',
            ]
        ];
    }
}
