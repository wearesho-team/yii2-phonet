<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190226133740CreateCallExternalTable
 */
class M190226133740CreateCallExternalTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('phonet_call_external', [
            'id' => $this->primaryKey(),
            'subject_number' => $this->string()->notNull(),
            'trunk_name' => $this->string()
                ->notNull()
                ->comment('External number'),
            'trunk_number' => $this->string()
                ->notNull()
                ->comment('Name of eternal number'),
            'call_id' => $this->integer()->notNull()
        ]);
        $this->addForeignKey(
            'phonet_call_external_call_fk',
            'phonet_call_external',
            'call_id',
            'phonet_call',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('phonet_call_external_call_fk', 'phonet_call_external');
        $this->dropTable('phonet_call_external');
    }
}
