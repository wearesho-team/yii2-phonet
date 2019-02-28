<?php

namespace Wearesho\Phonet\Yii\Call;

use MyCLabs\Enum\Enum;
use Wearesho\Phonet\Call\Direction;

/**
 * Class Pause
 * @package Wearesho\Phonet\Yii\Enum
 *
 * @method static Pause ON()
 * @method static Pause OFF()
 */
final class Pause extends Enum
{
    public const ON = Direction::PAUSE_ON;
    public const OFF = Direction::PAUSE_OFF;
}
