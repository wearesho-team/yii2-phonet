<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190239131201CreateCallEventTable
 */
class M190239131201CreateCallEventTable extends Migration
{
    public function safeUp(): void
    {
        $directionEnum = "enum ('1', '2', '4', '32', '64')";
        $eventEnum = "enum ('call.dial', 'call.bridge', 'call.hangup')";

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute("create type phonet_direction as $directionEnum");
            $directionEnum = 'phonet_direction';
            $this->execute("create type phonet_event as $eventEnum");
            $eventEnum = 'phonet_event';
        }

        $this->createTable('phonet_call_event', [
            'id' => $this->primaryKey(),
            'event' => $eventEnum,
            'domain' => $this->string(),
            'uuid' => $this->string(),
            'parent_uuid' => $this->string()->null(),
            'dial_at' => $this->timestamp(),
            'bridge_at' => $this->timestamp()->null(),
            'direction' => $directionEnum,
            'server_time' => $this->timestamp(),
            'employee_caller_id' => $this->integer(),
            'employee_call_taker_id' => $this->integer()->null(),
            'trunk_number' => $this->string(),
            'trunk_name' => $this->string(),
        ]);
        $this->addForeignKey(
            'phonet_subject_call_event',
            'phonet_subject',
            'call_event_id',
            'phonet_call_event',
            'id'
        );
        $this->addForeignKey(
            'phonet_call_event_employee_caller',
            'phonet_call_event',
            'employee_caller_id',
            'phonet_employee',
            'id'
        );
        $this->addForeignKey(
            'phonet_call_event_employee_call_taker',
            'phonet_call_event',
            'employee_call_taker_id',
            'phonet_employee',
            'id'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('phonet_subject_call_event', 'phonet_subject');
        $this->dropForeignKey('phonet_call_event_employee_caller', 'phonet_call_event');
        $this->dropForeignKey('phonet_call_event_employee_call_taker', 'phonet_call_event');
        $this->dropTable('phonet_call_event');

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute('drop type phonet_direction');
            $this->execute('drop type phonet_event');
        }
    }
}
