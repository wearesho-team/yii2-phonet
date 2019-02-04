<?php

namespace Wearesho\Phonet\Yii\Data;

use Carbon\Carbon;
use Wearesho\Phonet;

/**
 * Class CallEvent
 * @package Wearesho\Phonet\Yii\Data
 */
class CallEvent implements \JsonSerializable
{
    /** @var string */
    protected $event;

    /** @var string */
    protected $uuid;

    /** @var string|null */
    protected $parentUuid;

    /** @var string */
    protected $domain;

    /** @var Carbon */
    protected $dialAt;

    /** @var Carbon|null */
    protected $bridgeAt;

    /** @var Phonet\Enum\Direction */
    protected $direction;

    /** @var int|null */
    protected $serverTime;

    /** @var Phonet\Yii\Data\Employee */
    protected $employeeCaller;

    /** @var Phonet\Yii\Data\Employee|null */
    protected $employeeCallTaker;

    /** @var Phonet\Data\Collection\Subject|null */
    protected $subjects;

    /** @var string */
    protected $trunkNumber;

    /** @var string */
    protected $trunkName;

    public function __construct(
        string $event,
        string $uuid,
        ?string $parentUuid,
        string $domain,
        Carbon $dialAt,
        ?Carbon $bridgeAt,
        Phonet\Enum\Direction $direction,
        ?int $serverTime,
        Phonet\Yii\Data\Employee $employeeCaller,
        ?Phonet\Yii\Data\Employee $employeeCallTaker,
        ?Phonet\Data\Collection\Subject $subjects,
        string $trunkNumber,
        string $trunkName
    ) {
        $this->event = $event;
        $this->uuid = $uuid;
        $this->parentUuid = $parentUuid;
        $this->domain = $domain;
        $this->dialAt = $dialAt;
        $this->bridgeAt = $bridgeAt;
        $this->direction = $direction;
        $this->serverTime = $serverTime;
        $this->employeeCaller = $employeeCaller;
        $this->employeeCallTaker = $employeeCallTaker;
        $this->subjects = $subjects;
        $this->trunkNumber = $trunkNumber;
        $this->trunkName = $trunkName;
    }

    public function jsonSerialize(): array
    {
        return [
            'event' => $this->event,
            'uuid' => $this->uuid,
            'parentUuid' => $this->parentUuid,
            'domain' => $this->domain,
            'dialAt' => $this->dialAt,
            'bridgeAt' => $this->bridgeAt,
            'direction' => $this->direction,
            'serverTime' => $this->serverTime,
            'employeeCaller' => $this->employeeCaller,
            'employeeCallTaker' => $this->employeeCallTaker,
            'subjects' => $this->subjects,
            'trunkName' => $this->trunkName,
            'trunkNumber' => $this->trunkNumber,
        ];
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getParentUuid(): ?string
    {
        return $this->parentUuid;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getDialAt(): Carbon
    {
        return $this->dialAt;
    }

    public function getBridgeAt(): ?Carbon
    {
        return $this->bridgeAt;
    }

    public function getDirection(): Phonet\Enum\Direction
    {
        return $this->direction;
    }

    public function getServerTime(): ?int
    {
        return $this->serverTime;
    }

    public function getEmployeeCaller(): Phonet\Yii\Data\Employee
    {
        return $this->employeeCaller;
    }

    public function getEmployeeCallTaker(): ?Phonet\Yii\Data\Employee
    {
        return $this->employeeCallTaker;
    }

    public function getSubjects(): ?Phonet\Data\Collection\Subject
    {
        return $this->subjects;
    }

    public function getTrunkNumber(): string
    {
        return $this->trunkNumber;
    }

    public function getTrunkName(): string
    {
        return $this->trunkName;
    }
}
