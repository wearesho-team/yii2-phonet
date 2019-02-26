<?php

namespace Wearesho\Phonet\Yii\Record;

use yii\db;

/**
 * Class Employee
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $internal_number
 * @property string $display_name
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
            [['id', 'internal_number', 'display_name'], 'required'],
            [['id', 'user_id'], 'integer'],
            [['display_name', 'internal_number'], 'string']
        ];
    }
}
