<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete;

use Carbon\Carbon;
use Wearesho\Phonet;

/**
 * Class ReceiveTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete
 */
class ReceiveTest extends TestCase
{
    public function testSuccessFindInFirstBatch(): void
    {
        $calls = $this->getCompleteCallDataCollection(50);
        $calls[50] = $this->createCompleteCallData(static::UUID);

        $this->repository->expects($this->once())
            ->method('companyCalls')
            ->willReturn($calls);
        \Yii::$container->set(Phonet\Repository::class, $this->repository);

        $job = $this->createJob(static::UUID);

        $job->execute('queue');

        $completeCallData = Phonet\Yii\Record\Call\Complete\Data::find()->andWhere(['uuid' => static::UUID])->all();

        $this->assertCount(1, $completeCallData);
        $data = $completeCallData[0];

        $this->assertEquals(
            [
                'id' => 1,
                'uuid' => 'uuid',
                'transfer_history' => 'transfer-history',
                'status' => Phonet\Call\Complete\Status::TARGET_RESPONDED(),
                'duration' => 10,
                'bill_secs' => 10,
                'trunk' => 'trunk',
                'end_at' => Carbon::getTestNow()->toDateTimeString(),
                'audio_rec_url' => 'audio-rec-url',
                'subject_number' => 'subject-number',
                'subject_name' => 'subject-name'
            ],
            $data->attributes
        );
    }

    public function testSuccessFindInSecondBatch()
    {
        $firstCallsBatch = $this->getCompleteCallDataCollection(50);
        $secondCallsBatch = $this->getCompleteCallDataCollection(49);
        $secondCallsBatch[50] = $this->createCompleteCallData(static::UUID);

        $this->repository->expects($this->exactly(2))
            ->method('companyCalls')
            ->willReturn($firstCallsBatch, $secondCallsBatch);
        \Yii::$container->set(Phonet\Repository::class, $this->repository);

        $job = $this->createJob(static::UUID);

        $job->execute('queue');

        $completeCallData = Phonet\Yii\Record\Call\Complete\Data::find()->andWhere(['uuid' => static::UUID])->all();

        $this->assertCount(1, $completeCallData);
        $data = $completeCallData[0];

        $this->assertEquals(
            [
                'id' => 1,
                'uuid' => 'uuid',
                'transfer_history' => 'transfer-history',
                'status' => Phonet\Call\Complete\Status::TARGET_RESPONDED(),
                'duration' => 10,
                'bill_secs' => 10,
                'trunk' => 'trunk',
                'end_at' => Carbon::getTestNow()->toDateTimeString(),
                'audio_rec_url' => 'audio-rec-url',
                'subject_number' => 'subject-number',
                'subject_name' => 'subject-name'
            ],
            $data->attributes
        );
    }

    protected function getCompleteCallDataCollection(int $count = 50): Phonet\Call\Complete\Collection
    {
        return new Phonet\Call\Complete\Collection(
            array_fill(0, $count, $this->createCompleteCallData('invalid-uuid'))
        );
    }
}
