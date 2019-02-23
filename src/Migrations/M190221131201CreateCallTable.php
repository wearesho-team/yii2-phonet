<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\ColumnSchemaBuilder;
use yii\db\Migration;

/**
 * Class M190221131201CreateCallTable
 */
class M190221131201CreateCallTable extends Migration
{
    public function safeUp(): void
    {
        $typeEnum = "enum ('1', '2', '4')";
        $pauseEnum = "enum ('32', '64')";

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute("create type phonet_call_type as $typeEnum");
            $typeEnum = 'phonet_call_type';
            $this->execute("create type phonet_call_pause as $pauseEnum");
            $pauseEnum = 'phonet_event';
        }

        $this->createTable('phonet_call', [
            'id' => $this->primaryKey(),
            'domain' => $this->string(),
            'uuid' => $this->string()->unique(),
            'parent_uuid' => $this->string()->null(),
            'dial_at' => $this->timestamp(),
            'bridge_at' => $this->timestamp()->null(),
            'hangup_at' => $this->timestamp()->null(),
            'type' => $typeEnum,
            'operator_id' => $this->integer(),
            'pause' => "$pauseEnum default '64'",
        ]);
        $this->addForeignKey(
            'phonet_call_operator_employee_fk',
            'phonet_call',
            'operator_id',
            'phonet_employee',
            'id'
        );
        $this->addForeignKey(
            'phonet_call_employee_call_taker',
            'phonet_call',
            'employee_call_taker_id',
            'phonet_employee',
            'id'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('phonet_subject_call_event', 'phonet_subject');
        $this->dropForeignKey('phonet_call_employee_caller', 'phonet_call');
        $this->dropForeignKey('phonet_call_employee_call_taker', 'phonet_call');
        $this->dropTable('phonet_call');

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute('drop type phonet_direction');
            $this->execute('drop type phonet_event');
        }
    }
}
