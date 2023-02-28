<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 18:29 PM
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;
use App\Contracts\LocalizeFaDescription;

/**
 * @method static IS_GENERAL_FOR_PRODUCT()
 * @method static IS_GENERAL_FOR_USER()
 * @method static SOME_OF_USERS_HAVE_THIS()
 */
final class VoucherTypeEnum extends Enum implements LocalizedEnum, LocalizeFaDescription
{
  const IS_GENERAL_FOR_PRODUCT = 1;
  const IS_GENERAL_FOR_USER = 2;
  const SOME_OF_USERS_HAVE_THIS = 3;

  public static function getLocalizeFaDescription(): array
  {
    return [
      static::class => [
        'IS_GENERAL_FOR_PRODUCT' => 'همه محصولات',
        'IS_GENERAL_FOR_USER' => 'همه کاربران',
        'SOME_OF_USERS_HAVE_THIS' => 'بعضی کاربران'
      ]
    ];
  }
}
