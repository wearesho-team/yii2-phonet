<?php

namespace Wearesho\Phonet\Yii\Record;

use Kartavik\Yii2;
use Wearesho\Phonet\Enum\CompleteCallStatus;
use yii\db;

/**
 * Class CompleteCallData
 * @package Wearesho\Phonet\Yii\Record
 *
 * @property int $id
 * @property string $uuid
 * @property string $transfer_history
 * @property CompleteCallStatus $status
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
class CompleteCallData extends db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'phonet_complete_call_data';
    }

    public function behaviors(): array
    {
        return [
            'enum' => [
                'class' => Yii2\Behaviors\EnumMappingBehavior::class,
                'map' => [
                    'status' => CompleteCallStatus::class
                ],
                'attributesType' => [
                    'status' => 'integer',
                ]
            ]
        ];
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
            [['duration', 'bill_secs'], 'integer'],
            [
                'status',
                Yii2\Validators\EnumValidator::class,
                'targetEnum' => CompleteCallStatus::class
            ]
        ];
    }

    public function getCall(): db\ActiveQuery
    {
        return $this->hasOne(Call::class, ['id' => 'call_id']);
    }
}
