<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Record;

use Carbon\Carbon;
use Wearesho\Phonet\Call\Complete\Status;
use Wearesho\Phonet\Yii;

/**
 * Class CallTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Record
 * @coversDefaultClass \Wearesho\Phonet\Yii\Record\Call
 * @internal
 */
class CallTest extends Yii\Tests\Unit\TestCase
{
    protected const UUID = 'test-uuid';
    protected const DOMAIN = 'test-domain';
    protected const DIAL_AT = '2018-03-12 03:03:03';
    protected const UPDATED_AT = '2018-03-12 04:03:03';
    protected const DISPLAY_NAME = 'test-display-name';
    protected const INTERNAL_NUMBER = 'test-internal-number';
    protected const OPERATOR_ID = 1;
    protected const BRIDGE_AT = '2018-03-12 03:03:25';
    protected const SUBJECT_NUMBER = 'test-subject-number';
    protected const SUBJECT_NAME = 'test-subject-name';
    protected const AUDIO_REC_URL = 'test-audio-rec-url';
    protected const END_AT = '2018-03-12 03:04:01';
    protected const TRUNK = 'test-trunk';
    protected const TRANSFER_HISTORY = 'test-transfer-history';
    protected const BILL_SECS = 10;
    protected const DURATION = 10;

    /** @var Yii\Record\Call */
    protected $call;

    protected function setUp(): void
    {
        parent::setUp();

        $operator = new Yii\Record\Employee([
            'display_name' => static::DISPLAY_NAME,
            'internal_number' => static::INTERNAL_NUMBER,
            'id' => static::OPERATOR_ID,
        ]);
        $this->assertTrue($operator->save());
        $call = new Yii\Record\Call([
            'uuid' => static::UUID,
            'domain' => static::DOMAIN,
            'dial_at' => Carbon::make(static::DIAL_AT)->toDateTimeString(),
            'type' => Yii\Call\Type::INTERNAL(),
            'pause' => Yii\Call\Pause::OFF(),
            'updated_at' => Carbon::make(static::UPDATED_AT)->toDateTimeString(),
            'operator_id' => $operator->id,
            'bridge_at' => Carbon::make(static::BRIDGE_AT)->toDateTimeString()
        ]);
        $this->assertTrue($call->save());
        $data = new Yii\Record\CompleteCallData([
            'uuid' => static::UUID,
            'status' => Status::TARGET_RESPONDED(),
            'subject_number' => static::SUBJECT_NUMBER,
            'bill_secs' => static::BILL_SECS,
            'duration' => static::DURATION,
            'subject_name' => static::SUBJECT_NAME,
            'audio_rec_url' => static::AUDIO_REC_URL,
            'end_at' => Carbon::make(static::END_AT)->toDateTimeString(),
            'trunk' => static::TRUNK,
            'transfer_history' => static::TRANSFER_HISTORY,
        ]);
        $this->assertTrue($data->save());

        $this->call = Yii\Record\Call::find()->andWhere(['id' => $call->id])->one();
    }

    public function testGetIsInternal(): void
    {
        $this->assertTrue($this->call->isInternal);
    }

    public function testGetIsExternal(): void
    {
        $this->assertFalse($this->call->isExternal);
    }

    public function testGetUuid(): void
    {
        $this->assertEquals(static::UUID, $this->call->uuid);
    }

    public function testGetDomain(): void
    {
        $this->assertEquals(static::DOMAIN, $this->call->domain);
    }

    public function testGetDialAt(): void
    {
        $this->assertEquals(static::DIAL_AT, $this->call->dial_at);
    }

    public function testGetType(): void
    {
        $this->assertEquals(Yii\Call\Type::INTERNAL(), $this->call->type);
    }

    public function testGetPause(): void
    {
        $this->assertEquals(Yii\Call\Pause::OFF(), $this->call->pause);
    }

    public function testGetUpdatedAt(): void
    {
        $this->assertEquals(static::UPDATED_AT, $this->call->updated_at);
    }

    public function testGetOperatorId(): void
    {
        $this->assertEquals(static::OPERATOR_ID, $this->call->operator_id);
    }

    public function testGetBridgeAt(): void
    {
        $this->assertEquals(static::BRIDGE_AT, $this->call->bridge_at);
    }

    public function testGetOperatorDisplayName(): void
    {
        $this->assertEquals(static::DISPLAY_NAME, $this->call->operator->display_name);
    }

    public function testGetOperatorInternalNumber(): void
    {
        $this->assertEquals(static::INTERNAL_NUMBER, $this->call->operator->internal_number);
    }

    public function testGetDataAttributes(): void
    {
        $this->assertEquals(
            [
                'uuid' => static::UUID,
                'status' => Status::TARGET_RESPONDED(),
                'subject_number' => static::SUBJECT_NUMBER,
                'bill_secs' => static::BILL_SECS,
                'duration' => static::DURATION,
                'subject_name' => static::SUBJECT_NAME,
                'audio_rec_url' => static::AUDIO_REC_URL,
                'end_at' => Carbon::make(static::END_AT)->toDateTimeString(),
                'trunk' => static::TRUNK,
                'transfer_history' => static::TRANSFER_HISTORY,
                'id' => $this->call->completeData->id
            ],
            $this->call->completeData->attributes
        );
    }
}
