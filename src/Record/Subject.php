<?php

namespace Wearesho\Phonet\Yii\Record;

use yii\db;

/**
 * Class Subject
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property string|null $internal_id
 * @property string|null $name;
 * @property string $number
 * @property string|null $company
 * @property string $uri
 * @property string|null $priority
 * @property int $call_event_id
 *
 * @property-read CallEvent $callEvent
 */
class Subject extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_subject';
    }

    public function rules(): array
    {
        return [
            [['number', 'uri', 'call_event_id'], 'required'],
            [['number', 'uri', 'internal_id', 'name', 'company', 'priority'], 'string'],
            ['call_event_id', 'integer']
        ];
    }

    public function getCallEvent(): db\ActiveQuery
    {
        return $this->hasOne(CallEvent::class, ['id' => 'call_event_id']);
    }
}
