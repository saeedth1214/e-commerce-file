<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 18:29 PM
 */

namespace App\Enums;

use App\Contracts\LocalizeFaDescription;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static  Unseen()
 * @method static  Accept()
 * @method static  Reject()
 */
final class CommentStatusEnum extends Enum implements LocalizedEnum, LocalizeFaDescription
{
    const UNSEEN = 0;
    const ACCEPT = 1;
    const REJECT = 2;

    public static function getLocalizeFaDescription(): array
    {
        return [
            static::class => [
                'UNSEEN' => 'دیده نشده  ',
                'ACCEPT' => 'تایید شده',
                'REJECT' => 'رد شده',
            ]
        ];
    }
}
