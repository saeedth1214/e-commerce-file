<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static FORMAT()
 * @method static static SIZE()
 * @method static static ASPECT()
 * @method static static RESOLUTION()
 * @method static static SCREEN()
 */
final class AttributeTypeEnum extends Enum
{
    const FORMAT =   1;
    const SIZE =   2;
    const ASPECT = 3;
    const RESOLUTION = 4;
    const SCREEN = 5;
}
