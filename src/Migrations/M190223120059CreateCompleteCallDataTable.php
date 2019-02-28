<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190223120059CreateCompleteCallDataTable
 */
class M190223120059CreateCompleteCallDataTable extends Migration
{
    protected const ENUM_CALL_STATUS = 'phonet_call_status';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $statusEnum = "enum(
            'TARGET_RESPONDED',
            'TARGET_NOT_RESPONDED',
            'DIRECTION_OVERLOADED',
            'INTERNAL_ERROR',
            'TARGET_IS_BUSY'
        )";

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute("create type " . static::ENUM_CALL_STATUS . " as $statusEnum");
            $statusEnum = static::ENUM_CALL_STATUS;
        }

        $this->createTable('phonet_complete_call_data', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(36)->unique()->notNull(),
            'transfer_history' => $this->string()->null(),
            'status' => "$statusEnum not null",
            'duration' => $this->integer()->notNull(),
            'bill_secs' => $this->integer()->notNull(),
            'trunk' => $this->string()->null(),
            'end_at' => $this->timestamp()->notNull(),
            'audio_rec_url' => $this->string()->null(),
            'subject_number' => $this->string()->null(),
            'subject_name' => $this->string()->null(),
        ]);

        $this->addForeignKey(
            'phonet_complete_call_data_call_fk',
            'phonet_complete_call_data',
            'uuid',
            'phonet_call',
            'uuid'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('phonet_complete_call_data_call_fk', 'phonet_complete_call_data');
        $this->dropTable('phonet_complete_call_data');

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute('drop type ' . static::ENUM_CALL_STATUS);
        }
    }
}
