<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190223120059CreateCompleteCallTable
 */
class M190223120059CreateCompleteCallTable extends Migration
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

        $this->createTable('phonet_complete_call', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(36)
                ->unique()
                ->notNull()
                ->comment('Unique id of call in phonet service'),
            'transfer_history' => $this->string()
                ->null()
                ->comment('Way of call transferring'),
            'status' => "$statusEnum not null",
            'duration' => $this->integer()
                ->notNull()
                ->comment('Call duration starts from dial_at'),
            'bill_secs' => $this->integer()
                ->notNull()
                ->comment('Call duration starts from target answer'),
            'trunk' => $this->string(256)
                ->null()
                ->comment('External number'),
            'end_at' => $this->timestamp()
                ->notNull()
                ->comment('Time when call end'),
            'audio_rec_url' => $this->string(256)
                ->null()
                ->comment('Url to audio of call'),
            'subject_number' => $this->string(256)
                ->null()
                ->comment('Internal number of client'),
            'subject_name' => $this->string(256)
                ->null()
                ->comment('Display name of subject'),
        ]);

        $this->addForeignKey(
            'phonet_complete_call_call_fk',
            'phonet_complete_call',
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
        $this->dropForeignKey('phonet_complete_call_call_fk', 'phonet_complete_call');
        $this->dropTable('phonet_complete_call');

        if ($this->getDb()->getDriverName() === 'pgsql') {
            $this->execute('drop type ' . static::ENUM_CALL_STATUS);
        }
    }
}
