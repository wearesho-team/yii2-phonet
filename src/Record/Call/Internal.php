<?php

namespace Wearesho\Phonet\Yii\Record\Call;

use Wearesho\Phonet\Yii\Record;
use yii\db;

/**
 * Class Complete
 * @package Wearesho\Phonet\Yii\Record\Call
 *
 * @property int $id
 * @property int $operator_id
 * @property int $call_id
 *
 * @property-read Record\Call $call
 * @property-read Record\Employee $operator
 */
class Internal extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_call_internal';
    }

    public function rules(): array
    {
        return [
            [['operator_id', 'call_id'], 'required'],
            [['operator_id', 'call_id'], 'integer'],
        ];
    }

    public function getCall(): db\ActiveQuery
    {
        return $this->hasOne(Record\Call::class, ['id' => 'call_id']);
    }

    public function getOperator(): db\ActiveQuery
    {
        return $this->hasOne(Record\Employee::class, ['id' => 'operator_id']);
    }
}
