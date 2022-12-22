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
 * @method static static AdminAdded()
 * @method static static Payment()
 */
final class AccessTypeEnum extends Enum
{
    const AdminAdded = 0;
    const Payment = 1;
}
