<?php

namespace Wearesho\Phonet\Yii\Record\Call;

use Wearesho\Phonet\Yii\Record\Call;
use yii\db;

/**
 * Class Complete
 * @package Wearesho\Phonet\Yii\Record\Call
 *
 * @property int $id
 * @property string $subject_number
 * @property string|null $trunk_name
 * @property string|null $trunk_number
 * @property int $call_id
 *
 * @property-read Call $call
 */
class External extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_call_external';
    }

    public function rules(): array
    {
        return [
            [['subject_number', 'call_id'], 'required'],
            [['subject_number', 'trunk_number', 'trunk_name'], 'string'],
            ['call_id', 'integer'],
        ];
    }

    public function getCall(): db\ActiveQuery
    {
        return $this->hasOne(Call::class, ['id' => 'call_id']);
    }
}
