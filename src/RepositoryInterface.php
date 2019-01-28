<?php

namespace Wearesho\Phonet\Yii;

use Wearesho\Phonet\Yii\Model\CallEvent;

/**
 * Interface RepositoryInterface
 * @package Wearesho\Phonet\Yii
 */
interface RepositoryInterface
{
    public function put(CallEvent $call): void;
}
