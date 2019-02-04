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
    /** @var array */
    protected $calls;

    public function put(CallEvent $call): void
    {
        $this->calls[] = $call;
    }

    public function getCalls(): array
    {
        return $this->calls;
    }
}
