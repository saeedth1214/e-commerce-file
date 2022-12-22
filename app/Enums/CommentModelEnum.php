<?php
/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 18:29 PM
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use App\Models\Plan;
use App\Models\File;

/**
 * @method static static PLAN()
 * @method static static FILE()
 */
final class CommentModelEnum extends Enum
{
    const Plan = Plan::class;
    const File = File::class;
}
