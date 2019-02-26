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
        $statusEnum = "enum('0', '1', '2', '3', '4')";

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute("create type " . static::ENUM_CALL_STATUS . " as $statusEnum");
            $statusEnum = static::ENUM_CALL_STATUS;
        }

        $this->createTable('phonet_complete_call_data', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->unique()->notNull(),
            'transfer_history' => $this->string()->null(),
            'status' => $statusEnum,
            'duration' => $this->integer(),
            'bill_secs' => $this->integer(),
            'trunk' => $this->string()->null(),
            'end_at' => $this->timestamp(),
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
