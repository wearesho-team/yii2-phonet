<?php

namespace Wearesho\Phonet\Yii\Data;

use Carbon\Carbon;
use Wearesho\Phonet;
use yii\base;

/**
 * Class CallEvent
 * @package Wearesho\Phonet\Yii\Data
 *
 * @property string $event
 * @property string $uuid
 * @property string|null $parentUuid
 * @property string $domain
 * @property Carbon $dialAt
 * @property Carbon|null $bridgeAt
 * @property Phonet\Enum\Direction $direction
 * @property Carbon|null $serverTime
 * @property Employee $employeeCaller
 * @property Employee $employeeCallTaker
 * @property Phonet\Data\Collection\Subject $subjects
 * @property string $trunkNumber
 * @property string $trunkName
 */
class CallEvent extends base\Model implements \JsonSerializable
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

    /** @var Employee */
    protected $employeeCaller;

    /** @var Employee|null */
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
        ?Carbon $serverTime,
        Employee $employeeCaller,
        ?Employee $employeeCallTaker,
        ?Phonet\Data\Collection\Subject $subjects,
        string $trunkNumber,
        string $trunkName,
        array $config = []
    ) {
        parent::__construct($config);

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

    public function getServerTime(): ?Carbon
    {
        return $this->serverTime;
    }

    public function getEmployeeCaller(): Employee
    {
        return $this->employeeCaller;
    }

    public function getEmployeeCallTaker(): ?Employee
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
