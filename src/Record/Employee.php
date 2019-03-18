<?php

namespace Wearesho\Phonet\Yii\Record;

use Wearesho\Phonet\Yii\Record\Call\Internal\Internal;
use yii\db;

/**
 * Class Employee
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $internal_number
 * @property string $display_name
 *
 * @property-read Call[] $calls
 * @property-read Call\Internal[] $internalCallsData
 */
class Employee extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_employee';
    }

    public function rules(): array
    {
        return [
            [['internal_number', 'display_name'], 'required'],
            [['user_id'], 'integer'],
            [['display_name', 'internal_number'], 'string']
        ];
    }

    public function getCalls(): db\ActiveQuery
    {
        return $this->hasMany(Call::class, ['operator_id' => 'id']);
    }

    public function getInternalCallsData(): db\ActiveQuery
    {
        return $this->hasMany(Call\Internal::class, ['operator_id' => 'id']);
    }
}
