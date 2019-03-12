<?php

namespace Wearesho\Phonet\Yii\Call;

use MyCLabs\Enum\Enum;
use Wearesho\Phonet\Call\Direction;

/**
 * Class Type
 * @package Wearesho\Phonet\Yii\Enum
 *
 * @method static Type INTERNAL()
 * @method static Type EXTERNAL_OUT()
 * @method static Type EXTERNAL_IN()
 */
final class Type extends Enum
{
    public const INTERNAL = Direction::INTERNAL;
    public const EXTERNAL_OUT = Direction::OUT;
    public const EXTERNAL_IN = Direction::IN;
}
