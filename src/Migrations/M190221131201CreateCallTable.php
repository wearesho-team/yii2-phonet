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

    public function safeUp(): void
    {
        $typeEnum = "enum ('1', '2', '4')";
        $pauseEnum = "enum ('32', '64')";

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute("create type " . static::ENUM_CALL_TYPE . " as $typeEnum");
            $typeEnum = static::ENUM_CALL_TYPE;
            $this->execute("create type " . static::ENUM_EVENT . " as $pauseEnum");
            $pauseEnum = static::ENUM_EVENT;
        }

        $this->createTable('phonet_call', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->unique(),
            'parent_uuid' => $this->string()->null(),
            'domain' => $this->string(),
            'type' => $typeEnum,
            'operator_id' => $this->integer(),
            'pause' => "{$pauseEnum} default '64'",
            'dial_at' => $this->timestamp(),
            'bridge_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp(),
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
        }
    }
}
