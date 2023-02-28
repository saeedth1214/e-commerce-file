<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static  FORMAT()
 * @method static  SIZE()
 * @method static  ASPECT()
 * @method static  RESOLUTION()
 * @method static  SCREEN()
 */
final class AttributeTypeEnum extends Enum
{
    const FORMAT =   1;
    const SIZE =   2;
    const ASPECT = 3;
    const RESOLUTION = 4;
    const SCREEN = 5;
}
