<?php

namespace Wearesho\Phonet\Yii\Record;

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
 * @property Call\External\Data|null $externalData
 * @property Call\Internal\Data|null $internalData
 * @property Call\Complete\Data|null $completeData
 */
class Call extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_call';
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
                    'state',
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
        ];
    }

    public function getOperator(): db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'operator_id']);
    }

    public function getIsInternal(): bool
    {
        return $this->type === Phonet\Yii\Call\Type::INTERNAL()->getKey();
    }

    public function getIsExternal(): bool
    {
        return $this->type === Phonet\Yii\Call\Type::EXTERNAL_IN()->getKey()
            || $this->type === Phonet\Yii\Call\Type::EXTERNAL_OUT()->getKey();
    }

    public function getExternalData(): db\ActiveQuery
    {
        return $this->hasOne(Call\External\Data::class, ['call_id' => 'id']);
    }

    public function getInternalData(): db\ActiveQuery
    {
        return $this->hasOne(Call\Internal\Data::class, ['call_id' => 'id']);
    }

    public function getCompleteData(): db\ActiveQuery
    {
        return $this->hasOne(Call\Complete\Data::class, ['uuid' => 'uuid']);
    }
}
