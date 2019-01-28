<?php

namespace Wearesho\Phonet\Yii\Model;

use yii\base\Model;

/**
 * Class CallEvent
 * @package Wearesho\Phonet\Yii\Model
 *
 * @property int $id
 * @property string $event
 * @property string $uuid
 * @property string $parent_uuid
 * @property string $domain
 * @property int $dial_at
 * @property int|null $bridge_at
 * @property int $direction
 * @property int $server_time
 * @property array $employee_caller
 * @property array|null $employee_call_taker
 * @property array|null $subjects
 * @property string $trunk_number
 * @property string $trunk_name
 */
class CallEvent extends Model
{
    public function rules(): array
    {
        return [
            [
                [
                    'event',
                    'uuid',
                    'parent_uuid',
                    'domain',
                    'dial_at',
                    'bridge_at',
                    'direction',
                    'server_time',
                    'employee_caller',
                    'employee_call_taker',
                    'subjects',
                    'trunk_number',
                    'trunk_name'
                ],
                'required'
            ],
            [
                [
                    'event',
                    'uuid',
                    'parent_uuid',
                    'domain',
                    'trunk_number',
                    'trunk_name'
                ],
                'string'
            ],
            [
                [
                    'dial_at',
                    'bridge_at',
                    'server_time',
                    'direction',
                ],
                'integer'
            ],
        ];
    }
}
