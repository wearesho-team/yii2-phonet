<?php

namespace Wearesho\Phonet\Yii\Model;

/**
 * Class CallEvent
 * @package Wearesho\Phonet\Yii\Model
 *
 */
class CallEvent
{
    /** @var string */
    protected $event;

    /** @var string */
    protected $uuid;

    /** @var string|null */
    protected $parentUuid;

    /** @var string */
    protected $domain;

    /** @var int */
    protected $dialAt;

    /** @var int|null */
    protected $bridgeAt;

    /** @var int */
    protected $direction;

    /** @var int|null */
    protected $serverTime;

    /** @var array */
    protected $employeeCaller;

    /** @var array|null */
    protected $employeeCallTaker;

    /** @var array|null */
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
        int $dialAt,
        ?int $bridgeAt,
        int $direction,
        ?int $serverTime,
        array $employeeCaller,
        ?array $employeeCallTaker,
        ?array $subjects,
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

    public function getDialAt(): int
    {
        return $this->dialAt;
    }

    public function getBridgeAt(): ?int
    {
        return $this->bridgeAt;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function getServerTime(): ?int
    {
        return $this->serverTime;
    }

    public function getEmployeeCaller(): array
    {
        return $this->employeeCaller;
    }

    public function getEmployeeCallTaker(): ?array
    {
        return $this->employeeCallTaker;
    }

    public function getSubjects(): ?array
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
