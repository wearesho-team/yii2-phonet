<?php

namespace Wearesho\Phonet\Yii\Record\Call\External;

use Wearesho\Phonet\Yii\Record\Call;
use yii\db;

/**
 * Class Data
 * @package Wearesho\Phonet\Yii\Record\Call\External
 *
 * @property int $id
 * @property string $subject_number
 * @property string $trunk_name
 * @property string $trunk_number
 * @property int $call_id
 *
 * @property-read Call $call
 */
class Data extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_call_external_data';
    }

    public function rules(): array
    {
        return [
            [['subject_number', 'trunk_name', 'trunk_number', 'call_id'], 'required'],
            [['subject_number', 'trunk_number', 'trunk_name'], 'string'],
            ['call_id', 'integer'],
        ];
    }

    public function getCall(): db\ActiveQuery
    {
        return $this->hasOne(Call::class, ['id' => 'call_id']);
    }
}
