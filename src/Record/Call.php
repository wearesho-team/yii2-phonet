<?php

namespace Wearesho\Phonet\Yii\Record;

use Kartavik\Yii2\Behaviors\EnumMappingBehavior;
use Kartavik\Yii2\Validators\EnumValidator;
use Wearesho\Phonet;
use yii\db;

/**
 * Class Call
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property string $uuid
 * @property string|null $parent_uuid
 * @property string $domain
 * @property Phonet\Yii\Enum\CallType $type
 * @property int operator_id
 * @property Phonet\Yii\Enum\Pause $pause
 * @property string $dial_at
 * @property string|null $bridge_at
 * @property string $updated_at
 *
 * @property bool $isInternal
 * @property bool $isExternal
 *
 * @property Employee $operator
 * @property CallExternalData|CallInternalData $data
 * @property CompleteCallData $completeData
 */
class Call extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_call';
    }

    public function behaviors(): array
    {
        return [
            'enum' => [
                'class' => EnumMappingBehavior::class,
                'map' => [
                    'type' => Phonet\Yii\Enum\CallType::class,
                    'pause' => Phonet\Yii\Enum\Pause::class,
                ],
                'attributesType' => [
                    'type' => 'integer',
                    'pause' => 'integer',
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [
                [
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
                ['dial_at', 'bridge_at', 'updated_at'],
                'datetime',
                'format' => 'php:Y-m-d H:i:s'
            ],
            [
                'type',
                EnumValidator::class,
                'targetEnum' => Phonet\Yii\Enum\CallType::class,
            ],
            [
                'pause',
                EnumValidator::class,
                'targetEnum' => Phonet\Yii\Enum\Pause::class,
            ]
        ];
    }

    public function getOperator(): db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'operator_id']);
    }

    public function getIsInternal(): bool
    {
        return $this->type->equals(Phonet\Yii\Enum\CallType::INTERNAL());
    }

    public function getIsExternal(): bool
    {
        return $this->type->equals(Phonet\Yii\Enum\CallType::EXTERNAL_IN())
            || $this->type->equals(Phonet\Yii\Enum\CallType::EXTERNAL_OUT());
    }

    public function getData(): db\ActiveQuery
    {
        $relation = ['call_id' => 'id'];

        if ($this->isInternal) {
            return $this->hasOne(CallInternalData::class, $relation);
        } else {
            return $this->hasOne(CallExternalData::class, $relation);
        }
    }
}
