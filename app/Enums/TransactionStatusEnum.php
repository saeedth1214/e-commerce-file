<?php
/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 18:26 PM
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static Created()
 * @method static Paying()
 * @method static Payed()
 * @method static Canceled()
 */
final class TransactionStatusEnum extends Enum
{
    const Created = 0;
    const Paying = 1;
    const Payed = 2;
    const Canceled = 3;
}
