<?php

namespace Wearesho\Phonet\Yii\Record;

use yii\db;

/**
 * Class CallInternalData
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property int $operator_id
 * @property int $call_id
 *
 * @property-read Call $call
 */
class CallInternalData extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_call_internal_data';
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
        return $this->hasOne(Call::class, ['id' => 'call_id']);
    }
}
