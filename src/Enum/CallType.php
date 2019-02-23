<?php

namespace Wearesho\Phonet\Yii\Enum;

use MyCLabs\Enum\Enum;
use Wearesho\Phonet\Enum\Direction;

/**
 * Class CallType
 * @package Wearesho\Phonet\Yii\Enum
 *
 * @method static CallType INTERNAL()
 * @method static CallType EXTERNAL_OUT()
 * @method static CallType EXTERNAL_IN()
 */
final class CallType extends Enum
{
    public const INTERNAL = Direction::INTERNAL;
    public const EXTERNAL_OUT = Direction::OUT;
    public const EXTERNAL_IN = Direction::IN;
}
