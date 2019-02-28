<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190221131201CreateCallTable
 */
class M190221131201CreateCallTable extends Migration
{
    protected const ENUM_CALL_TYPE = 'phonet_call_type';
    protected const ENUM_EVENT = 'phonet_event';
    protected const ENUM_STATE = 'phonet_call_state';

    public function safeUp(): void
    {
        $typeEnum = "enum ('INTERNAL', 'EXTERNAL_OUT', 'EXTERNAL_IN')";
        $pauseEnum = "enum ('ON', 'OFF')";
        $stateEnum = "enum ('call.dial', 'call.bridge', 'call.hangup')";

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute("create type " . static::ENUM_CALL_TYPE . " as $typeEnum");
            $typeEnum = static::ENUM_CALL_TYPE;
            $this->execute("create type " . static::ENUM_EVENT . " as $pauseEnum");
            $pauseEnum = static::ENUM_EVENT;
            $this->execute("create type " . static::ENUM_STATE . " as $stateEnum");
            $stateEnum = static::ENUM_STATE;
        }

        $this->createTable('phonet_call', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(36)->unique()->notNull(),
            'parent_uuid' => $this->string()->null(),
            'domain' => $this->string()->notNull(),
            'type' => "$typeEnum not null",
            'operator_id' => $this->integer()->notNull(),
            'pause' => "{$pauseEnum} not null default 'OFF'",
            'dial_at' => $this->timestamp()->notNull(),
            'bridge_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->notNull(),
            'state' => "$stateEnum not null"
        ]);
        $this->addForeignKey(
            'phonet_call_operator_employee_fk',
            'phonet_call',
            'operator_id',
            'phonet_employee',
            'id'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('phonet_call_operator_employee_fk', 'phonet_call');
        $this->dropTable('phonet_call');

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute('drop type ' . static::ENUM_CALL_TYPE);
            $this->execute('drop type ' . static::ENUM_EVENT);
            $this->execute('drop type ' . static::ENUM_STATE);
        }
    }
}
