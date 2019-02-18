<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Data;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Wearesho\Phonet;

/**
 * Class CallEventTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Data
 */
class CallEventTest extends TestCase
{
    protected const UUID = 'test-uuid';
    protected const PARENT_UUID = 'test-parent-uuid';
    protected const DOMAIN = 'test-domain';
    protected const DIAL_AT = '2018-03-12';
    protected const BRIDGE_AT = '2018-03-12';
    protected const SERVER_TIME = '2018-03-12';
    protected const EMPLOYEE_ID = 1;
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

    /** @var Phonet\Yii\Data\CallEvent */
    protected $callEvent;

    protected function setUp(): void
    {
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
        $this->assertEquals(static::DIAL_AT, $this->callEvent->dialAt->toDateString());
    }

    public function getBridgeAt(): void
    {
        $this->assertEquals(static::BRIDGE_AT, $this->callEvent->getBridgeAt());
    }

    public function testGetDirection(): void
    {
        $this->assertEquals(Phonet\Enum\Direction::IN(), $this->callEvent->getDirection());
    }

    public function testGetServerTime(): void
    {
        $this->assertEquals(static::SERVER_TIME, $this->callEvent->getServerTime()->toDateString());
    }

    public function testGetEmployeeCaller(): void
    {
        $this->assertEquals(
            new Phonet\Yii\Data\Employee(
                static::EMPLOYEE_ID,
                static::INTERNAL_NUMBER,
                static::DISPLAY_NAME
            ),
            $this->callEvent->getEmployeeCaller()
        );
    }

    public function testGetEmployeeCallTaker(): void
    {
        $this->assertEquals(
            new Phonet\Yii\Data\Employee(
                static::EMPLOYEE_ID,
                static::INTERNAL_NUMBER,
                static::DISPLAY_NAME
            ),
            $this->callEvent->getEmployeeCallTaker()
        );
    }

    public function testGetSubjects(): void
    {
        $this->assertEquals(
            new Phonet\Data\Collection\Subject([
                new Phonet\Yii\Data\Subject(
                    static::NUMBER,
                    static::URI,
                    static::SUBJECT_ID,
                    static::SUBJECT_NAME,
                    static::COMPANY,
                    static::PRIORITY
                )
            ]),
            $this->callEvent->getSubjects()
        );
    }

    public function testGetTrunkNumber(): void
    {
        $this->assertEquals(static::TRUNK_NUMBER, $this->callEvent->getTrunkNumber());
    }

    public function testGetTrunkName(): void
    {
        $this->assertEquals(static::TRUNK_NAME, $this->callEvent->getTrunkName());
    }

    public function testJsonSerialize(): void
    {
        $this->assertEquals(
            [
                'event' => Phonet\Enum\Event::DIAL(),
                'uuid' => static::UUID,
                'parentUuid' => static::PARENT_UUID,
                'domain' => static::DOMAIN,
                'dialAt' => Carbon::make(static::DIAL_AT),
                'bridgeAt' => Carbon::make(static::BRIDGE_AT),
                'direction' => Phonet\Enum\Direction::IN(),
                'serverTime' => Carbon::make(static::SERVER_TIME),
                'employeeCaller' => new Phonet\Yii\Data\Employee(
                    static::EMPLOYEE_ID,
                    static::INTERNAL_NUMBER,
                    static::DISPLAY_NAME
                ),
                'employeeCallTaker' => new Phonet\Yii\Data\Employee(
                    static::EMPLOYEE_ID,
                    static::INTERNAL_NUMBER,
                    static::DISPLAY_NAME
                ),
                'subjects' => new Phonet\Data\Collection\Subject([
                    new Phonet\Yii\Data\Subject(
                        static::NUMBER,
                        static::URI,
                        static::SUBJECT_ID,
                        static::SUBJECT_NAME,
                        static::COMPANY,
                        static::PRIORITY
                    )
                ]),
                'trunkName' => static::TRUNK_NAME,
                'trunkNumber' => static::TRUNK_NUMBER,
            ],
            $this->callEvent->jsonSerialize()
        );
    }

    protected function createCallEvent(): Phonet\Yii\Data\CallEvent
    {
        return new Phonet\Yii\Data\CallEvent(
            Phonet\Enum\Event::DIAL(),
            static::UUID,
            static::PARENT_UUID,
            static::DOMAIN,
            Carbon::make(static::DIAL_AT),
            Carbon::make(static::BRIDGE_AT),
            Phonet\Enum\Direction::IN(),
            Carbon::make(static::SERVER_TIME),
            new Phonet\Yii\Data\Employee(
                static::EMPLOYEE_ID,
                static::INTERNAL_NUMBER,
                static::DISPLAY_NAME
            ),
            new Phonet\Yii\Data\Employee(
                static::EMPLOYEE_ID,
                static::INTERNAL_NUMBER,
                static::DISPLAY_NAME
            ),
            new Phonet\Data\Collection\Subject([
                new Phonet\Yii\Data\Subject(
                    static::NUMBER,
                    static::URI,
                    static::SUBJECT_ID,
                    static::SUBJECT_NAME,
                    static::COMPANY,
                    static::PRIORITY
                )
            ]),
            static::TRUNK_NUMBER,
            static::TRUNK_NAME
        );
    }
}
