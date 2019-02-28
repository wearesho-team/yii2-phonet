<?php

namespace Wearesho\Phonet\Yii\Record\Call\Internal;

use Wearesho\Phonet\Yii\Record\Call;
use yii\db;

/**
 * Class Data
 * @package Wearesho\Phonet\Yii\Record\Call\Internal
 *
 * @property int $id
 * @property int $operator_id
 * @property int $call_id
 *
 * @property-read Call $call
 */
class Data extends db\ActiveRecord
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
