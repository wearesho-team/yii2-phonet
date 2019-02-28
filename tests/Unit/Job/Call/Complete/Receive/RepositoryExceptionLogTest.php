<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete\Receive;

use Wearesho\Phonet\Exception;
use Wearesho\Phonet\Repository;
use Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete\TestCase;

/**
 * Class RepositoryExceptionLogTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete\Receive
 */
class RepositoryExceptionLogTest extends TestCase
{
    protected const MESSAGE = 'Exception message';

    public function testExceptionGetCalls(): void
    {
        $this->repository->expects($this->once())
            ->method('companyCalls')
            ->willThrowException(new Exception(static::MESSAGE));

        \Yii::$container->set(Repository::class, $this->repository);

        $job = $this->createJob(static::UUID);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(static::MESSAGE);

        $job->execute('queue');
    }

    protected function tearDown(): void
    {
        $log = \Yii::getLogger()->messages[18];

        $this->assertEquals(static::MESSAGE, $log[0]);

        parent::tearDown();
    }
}
