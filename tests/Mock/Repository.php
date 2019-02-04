<?php

namespace Wearesho\Phonet\Yii\Tests\Mock;

use Wearesho\Phonet;

/**
 * Class Repository
 * @package Wearesho\Phonet\Yii\Tests\Mock
 */
class Repository implements Phonet\Yii\RepositoryInterface
{
    /** @var array */
    protected $calls;

    public function put(Phonet\Yii\CallEvent $call): void
    {
        $this->calls[] = $call;
    }

    public function getCalls(): array
    {
        return $this->calls;
    }
}
