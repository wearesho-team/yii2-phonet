<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete;

use Carbon\Carbon;
use PHPUnit\Framework\MockObject\MockObject;
use Wearesho\Phonet;
use yii\log\Logger;

/**
 * Class TestCase
 * @package Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete
 */
abstract class TestCase extends Phonet\Yii\Tests\Unit\TestCase
{

    protected const PARENT_UUID = 'parent-uuid';
    protected const UUID = 'uuid';
    protected const ID = 1;
    protected const INTERNAL_NUMBER = 'internal-number';
    protected const DISPLAY_NAME = 'display-name';
    protected const TYPE = 1;
    protected const EMAIL = 'email';
    protected const SUBJECT_NUMBER = 'subject-number';
    protected const SUBJECT_NAME = 'subject-name';
    protected const DISPOSITION = 10;
    protected const TRUNK = 'trunk';
    protected const BILL_SECS = 10;
    protected const DURATION = 10;
    protected const TRANSFER_HISTORY = 'transfer-history';
    protected const AUDIO_REC_URL = 'audio-rec-url';

    /** @var Phonet\Repository|MockObject */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        \Yii::setLogger(new Logger());
        Carbon::setTestNow(Carbon::now());

        $this->repository = $this->createMock(Phonet\Repository::class);

        $operator = new Phonet\Yii\Record\Employee([
            'id' => 10,
            'internal_number' => 'number',
            'display_name' => 'display-name',
        ]);
        $this->assertTrue($operator->save(), implode($operator->getErrorSummary(true)));
        $call = new Phonet\Yii\Record\Call([
            'uuid' => static::UUID,
            'state' => Phonet\Call\Event::HANGUP,
            'domain' => 'domain',
            'dial_at' => Carbon::now()->toDateTimeString(),
            'type' => Phonet\Call\Direction::IN,
            'pause' => Phonet\Yii\Call\Pause::OFF,
            'operator_id' => $operator->id,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
        $this->assertTrue($call->save(), implode($call->getErrorSummary(true)));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    protected function createJob(string $uuid): Phonet\Yii\Job\Call\Complete\Receive
    {
        return new Phonet\Yii\Job\Call\Complete\Receive($this->repository, $uuid, Carbon::now(), Carbon::now());
    }

    protected function createCompleteCallData(string $uuid): Phonet\Call\Complete
    {
        return new Phonet\Call\Complete(
            $uuid,
            Phonet\Call\Direction::INTERNAL(),
            new Phonet\Employee(
                static::ID,
                static::INTERNAL_NUMBER,
                static::DISPLAY_NAME,
                static::TYPE,
                static::EMAIL
            ),
            Carbon::getTestNow(),
            Phonet\Call\Complete\Status::TARGET_RESPONDED(),
            static::BILL_SECS,
            static::DURATION,
            static::PARENT_UUID,
            new Phonet\Employee(
                static::ID,
                static::INTERNAL_NUMBER,
                static::DISPLAY_NAME,
                static::TYPE,
                static::EMAIL
            ),
            static::SUBJECT_NUMBER,
            static::SUBJECT_NAME,
            static::TRUNK,
            static::TRANSFER_HISTORY,
            static::AUDIO_REC_URL
        );
    }
}
