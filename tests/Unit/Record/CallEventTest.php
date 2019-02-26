<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Record;

use Carbon\Carbon;
use Wearesho\Phonet;

/**
 * Class CallEventTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Record
 */
class CallEventTest extends Phonet\Yii\Tests\Unit\TestCase
{
    protected const UUID = 'test-uuid';
    protected const PARENT_UUID = 'test-parent-uuid';
    protected const DOMAIN = 'test-domain';
    protected const DIAL_AT = '2018-03-12';
    protected const BRIDGE_AT = '2018-03-12';
    protected const SERVER_TIME = '2018-03-12';
    protected const EMPLOYEE_CALLER_ID = 1;
    protected const EMPLOYEE_CALL_TAKER_ID = 2;
    protected const INTERNAL_NUMBER = 'test-internal-number';
    protected const DISPLAY_NAME = 'test-display-name';
    protected const NUMBER = 'test-number';
    protected const URI = 'test-uri';
    protected const SUBJECT_ID = 'test-subject-id';
    protected const SUBJECT_NAME = 'test-subject-name';
    protected const COMPANY = 'test-company';
    protected const PRIORITY = 'test-priority';
    protected const TRUNK_NUMBER = 'test-trunk-number';
    protected const TRUNK_NAME = 'test-trunk-name';

    /** @var Phonet\Yii\Record\Call */
    protected $callEvent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->callEvent = $this->createCallEvent();
    }

    public function testGetEvent(): void
    {
        $this->assertEquals(Phonet\Enum\Event::DIAL, $this->callEvent->event->getValue());
    }

    public function testGetUuid(): void
    {
        $this->assertEquals(static::UUID, $this->callEvent->uuid);
    }

    public function testGetDomain(): void
    {
        $this->assertEquals(static::DOMAIN, $this->callEvent->domain);
    }

    public function testGetDialAt(): void
    {
        $this->assertEquals(Carbon::make(static::DIAL_AT)->toDateTimeString(), $this->callEvent->dial_at);
    }

    public function getBridgeAt(): void
    {
        $this->assertEquals(Carbon::make(static::BRIDGE_AT)->toDateTimeString(), $this->callEvent->bridge_at);
    }

    public function testGetDirection(): void
    {
        $this->assertEquals(Phonet\Enum\Direction::IN(), $this->callEvent->direction);
    }

    public function testGetServerTime(): void
    {
        $this->assertEquals(Carbon::make(static::SERVER_TIME)->toDateTimeString(), $this->callEvent->server_time);
    }

    public function testGetEmployeeCaller(): void
    {
        $this->assertEquals(
            (new Phonet\Yii\Record\Employee([
                'id' => static::EMPLOYEE_CALLER_ID,
                'internal_number' => static::INTERNAL_NUMBER,
                'display_name' => static::DISPLAY_NAME
            ]))->attributes,
            $this->callEvent->employeeCaller->attributes
        );
    }

    public function testGetEmployeeCallTaker(): void
    {
        $this->assertEquals(
            (new Phonet\Yii\Record\Employee([
                'id' => static::EMPLOYEE_CALL_TAKER_ID,
                'internal_number' => static::INTERNAL_NUMBER,
                'display_name' => static::DISPLAY_NAME
            ]))->attributes,
            $this->callEvent->employeeCallTaker->attributes
        );
    }

    public function testGetSubjects(): void
    {
        $this->assertEquals(
            (new Phonet\Yii\Record\Subject([
                'id' => 1,
                'number' => static::NUMBER,
                'uri' => static::URI,
                'internal_id' => static::SUBJECT_ID,
                'name' => static::SUBJECT_NAME,
                'company' => static::COMPANY,
                'priority' => static::PRIORITY,
                'call_event_id' => 1
            ]))->attributes,
            $this->callEvent->subjects[0]->attributes
        );
    }

    public function testGetTrunkNumber(): void
    {
        $this->assertEquals(static::TRUNK_NUMBER, $this->callEvent->trunk_number);
    }

    public function testGetTrunkName(): void
    {
        $this->assertEquals(static::TRUNK_NAME, $this->callEvent->trunk_name);
    }

    protected function createCallEvent(): Phonet\Yii\Record\Call
    {
        $employeeCaller = new Phonet\Yii\Record\Employee([
            'id' => static::EMPLOYEE_CALLER_ID,
            'internal_number' => static::INTERNAL_NUMBER,
            'display_name' => static::DISPLAY_NAME
        ]);
        $this->assertTrue($employeeCaller->save(), $this->formModelErrors($employeeCaller));
        $employeeCallTaker = new Phonet\Yii\Record\Employee([
            'id' => self::EMPLOYEE_CALL_TAKER_ID,
            'internal_number' => static::INTERNAL_NUMBER,
            'display_name' => static::DISPLAY_NAME
        ]);
        $this->assertTrue($employeeCallTaker->save(), $this->formModelErrors($employeeCallTaker));
        $event = new Phonet\Yii\Record\Call([
            'event' => Phonet\Enum\Event::DIAL(),
            'uuid' => static::UUID,
            'parent_uuid' => static::PARENT_UUID,
            'domain' => static::DOMAIN,
            'dial_at' => Carbon::make(static::DIAL_AT)->toDateTimeString(),
            'bridge_at' => Carbon::make(static::BRIDGE_AT)->toDateTimeString(),
            'direction' => Phonet\Enum\Direction::IN(),
            'server_time' => Carbon::make(static::SERVER_TIME)->toDateTimeString(),
            'employeeCaller' => $employeeCaller,
            'employeeCallTaker' => $employeeCallTaker,
            'trunk_number' => static::TRUNK_NUMBER,
            'trunk_name' => static::TRUNK_NAME
        ]);
        $this->assertTrue($event->save(), $this->formModelErrors($event));
        $subject = new Phonet\Yii\Record\Subject([
            'number' => static::NUMBER,
            'uri' => static::URI,
            'internal_id' => static::SUBJECT_ID,
            'name' => static::SUBJECT_NAME,
            'company' => static::COMPANY,
            'priority' => static::PRIORITY,
            'call_event_id' => $event->id
        ]);
        $this->assertTrue($subject->save(), $this->formModelErrors($subject));

        $event->refresh();

        return $event;
    }
}
