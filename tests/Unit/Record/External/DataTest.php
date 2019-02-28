<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Record\External;

use Carbon\Carbon;
use Wearesho\Phonet\Call\Event;
use Wearesho\Phonet\Yii;

/**
 * Class DataTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Record\External
 */
class DataTest extends Yii\Tests\Unit\TestCase
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

    /** @var Yii\Record\Employee */
    protected $operator;

    /** @var Yii\Record\Call\External\Data */
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operator = new Yii\Record\Employee([
            'display_name' => static::DISPLAY_NAME,
            'internal_number' => static::INTERNAL_NUMBER,
            'id' => static::OPERATOR_ID,
        ]);
        $this->assertTrue($this->operator->save());
        $call = new Yii\Record\Call([
            'uuid' => static::UUID,
            'domain' => static::DOMAIN,
            'dial_at' => Carbon::make(static::DIAL_AT)->toDateTimeString(),
            'type' => Yii\Call\Type::INTERNAL()->getKey(),
            'pause' => Yii\Call\Pause::OFF()->getKey(),
            'updated_at' => Carbon::make(static::UPDATED_AT)->toDateTimeString(),
            'operator_id' => $this->operator->id,
            'bridge_at' => Carbon::make(static::BRIDGE_AT)->toDateTimeString(),
            'state' => Event::DIAL,
        ]);
        $this->assertTrue($call->save());
        $this->data = new Yii\Record\Call\External\Data([
            'subject_number' => 'number',
            'trunk_name' => 'name',
            'trunk_number' => 'number',
            'call_id' => $call->id
        ]);
        $this->assertTrue($this->data->save());
        $this->data = Yii\Record\Call\External\Data::find()->andWhere(['id' => $this->data->id])->one();
    }

    public function testGetSubjectNumber(): void
    {
        $this->assertEquals('number', $this->data->subject_number);
    }

    public function testGetTrunkNumber(): void
    {
        $this->assertEquals('number', $this->data->trunk_number);
    }

    public function testGetTrunkName(): void
    {
        $this->assertEquals('name', $this->data->trunk_name);
    }

    public function testGetCall(): void
    {
        $this->assertNotEmpty($this->data->call);
        $this->assertInstanceOf(Yii\Record\Call::class, $this->data->call);
    }
}
