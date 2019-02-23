<?php

namespace Wearesho\Phonet\Yii\Record;

use Kartavik\Yii2\Behaviors\EnumMappingBehavior;
use Kartavik\Yii2\Validators\EnumValidator;
use Wearesho\Phonet;
use yii\db;

/**
 * Class CallEvent
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property Phonet\Enum\Event $event
 * @property string $uuid
 * @property string|null $parent_uuid
 * @property string $domain
 * @property string $dial_at
 * @property string|null $bridge_at
 * @property Phonet\Enum\Direction $direction
 * @property string|null $server_time
 * @property int $employee_caller_id
 * @property int $employee_call_taker_id
 * @property string $trunk_number
 * @property string $trunk_name
 *
 * @property Employee $employeeCaller
 * @property Employee $employeeCallTaker
 */
class CallEvent extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_call_event';
    }

    public function behaviors(): array
    {
        return [
            'enum' => [
                'class' => EnumMappingBehavior::class,
                'map' => [
                    'event' => Phonet\Enum\Event::class,
                    'direction' => Phonet\Enum\Direction::class,
                ],
                'attributesType' => [
                    'direction' => 'integer',
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [
                [
                    'event',
                    'uuid',
                    'domain',
                    'dial_at',
                    'direction',
                    'employee_caller_id',
                    'trunk_number',
                    'trunk_name'
                ],
                'required'
            ],
            [
                [
                    'uuid',
                    'parent_uuid',
                    'domain',
                    'trunk_number',
                    'trunk_name'
                ],
                'string'
            ],
            [
                ['dial_at', 'bridge_at', 'server_time'],
                'datetime',
                'format' => 'php:Y-m-d H:i:s'
            ],
            [
                'event',
                EnumValidator::class,
                'targetEnum' => Phonet\Enum\Event::class,
            ],
            [
                'direction',
                EnumValidator::class,
                'targetEnum' => Phonet\Enum\Direction::class,
            ]
        ];
    }

    public function getEmployeeCaller(): db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_caller_id']);
    }

    public function setEmployeeCaller(Employee $employee): self
    {
        $this->employee_caller_id = $employee->id;
        $this->populateRelation('employeeCaller', $employee);

        return $this;
    }

    public function getEmployeeCallTaker(): db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_call_taker_id']);
    }

    public function setEmployeeCallTaker(?Employee $employee): self
    {
        if ($employee !== null) {
            $this->employee_call_taker_id = $employee->id;
            $this->populateRelation('employeeCallTaker', $employee);
        }

        return $this;
    }
}
