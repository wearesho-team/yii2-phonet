<?php

namespace Wearesho\Phonet\Yii\Record\Call\Complete;

use Wearesho\Phonet\Call\Complete\Status;
use Wearesho\Phonet\Yii\Record\Call;
use yii\db;

/**
 * Class Data
 * @package Wearesho\Phonet\Yii\Record\Call\Complete
 *
 * @property int $id
 * @property string $uuid
 * @property string $transfer_history
 * @property int $status
 * @property int $duration
 * @property int $bill_secs
 * @property string $trunk
 * @property string $end_at
 * @property string $audio_rec_url
 * @property string $subject_number
 * @property string $subject_name
 *
 * @property-read Call $call
 */
class Data extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_complete_call_data';
    }

    public function rules(): array
    {
        return [
            [
                [
                    'uuid',
                    'status',
                    'duration',
                    'duration',
                    'bill_secs',
                    'end_at',
                ],
                'required'
            ],
            [
                [
                    'uuid',
                    'transfer_history',
                    'trunk',
                    'end_at',
                    'audio_rec_url',
                    'subject_name',
                    'subject_number'
                ],
                'string'
            ],
            ['end_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['duration', 'bill_secs'], 'integer'],
        ];
    }

    public function getCall(): db\ActiveQuery
    {
        return $this->hasOne(Call::class, ['id' => 'call_id']);
    }
}
