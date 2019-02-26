<?php

namespace Wearesho\Phonet\Yii\Record;

use yii\db;

/**
 * Class CallExternalData
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property string $subject_number
 * @property string $trunk_name
 * @property string $trunk_number
 * @property int $call_id
 */
class CallExternalData extends db\ActiveRecord
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
}
