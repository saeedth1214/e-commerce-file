<?php
/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 18:29 PM
 */

namespace App\Enums;

use BenSampo\Enum\Enum;


/**
 * @method static IS_GENERAL()
 * @method static OTHER()
 */
final class FilterOperatorEnum extends Enum
{
    const exact='=';
    const like = 'like';
}
