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
 * @property Phonet\Yii\Call\Type $type
 * @property int operator_id
 * @property Phonet\Yii\Call\Pause $pause
 * @property string $dial_at
 * @property string|null $bridge_at
 * @property string $updated_at
 * @property Phonet\Enum\Event $state
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
                    'type' => Phonet\Yii\Call\Type::class,
                    'pause' => Phonet\Yii\Call\Pause::class,
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
                    'type',
                    'pause',
                    'updated_at',
                    'operator_id',
                ],
                'required'
            ],
            [
                [
                    'uuid',
                    'parent_uuid',
                    'domain',
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
                'targetEnum' => Phonet\Yii\Call\Type::class,
            ],
            [
                'pause',
                EnumValidator::class,
                'targetEnum' => Phonet\Yii\Call\Pause::class,
            ]
        ];
    }

    public function getOperator(): db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'operator_id']);
    }

    public function getIsInternal(): bool
    {
        return $this->type->equals(Phonet\Yii\Call\Type::INTERNAL());
    }

    public function getIsExternal(): bool
    {
        return $this->type->equals(Phonet\Yii\Call\Type::EXTERNAL_IN())
            || $this->type->equals(Phonet\Yii\Call\Type::EXTERNAL_OUT());
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

    public function getCompleteData(): db\ActiveQuery
    {
        return $this->hasOne(CompleteCallData::class, ['uuid' => 'uuid']);
    }
}
