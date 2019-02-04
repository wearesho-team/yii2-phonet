<?php

namespace Wearesho\Phonet\Yii;

/**
 * Interface RepositoryInterface
 * @package Wearesho\Phonet\Yii
 */
interface RepositoryInterface
{
    public function put(CallEvent $call): void;
}
