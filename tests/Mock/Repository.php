<?php

namespace Wearesho\Phonet\Yii\Tests\Mock;

use Wearesho\Phonet\Yii\Model\CallEvent;
use Wearesho\Phonet\Yii\RepositoryInterface;

/**
 * Class Repository
 * @package Wearesho\Phonet\Yii\Tests\Mock
 */
class Repository implements RepositoryInterface
{
    public function put(CallEvent $call): void
    {
        // client side logic
    }
}
