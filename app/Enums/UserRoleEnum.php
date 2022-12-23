<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 18:26 PM
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;
use App\Contracts\LocalizeFaDescription;

/**
 * @method static static Admin()
 * @method static static Normal()
 */
final class UserRoleEnum extends Enum implements LocalizedEnum, LocalizeFaDescription
{
    const ADMIN = 1;
    const NORMAL = 0;

    public static function getLocalizeFaDescription(): array
    {
        return [
            static::class => [

                'ADMIN' => 'ادمین',
                'NORMAL' => 'کاربر عادی'
            ]

        ];
    }
}
