<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190226133740CreateCallExternalDataTable
 */
class M190226133740CreateCallExternalDataTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('phonet_call_external_data', [
            'id' => $this->primaryKey(),
            'subject_number' => $this->string(),
            'trunk_name' => $this->string(),
            'trunk_number' => $this->string(),
            'call_id' => $this->integer()
        ]);
        $this->addForeignKey(
            'phonet_call_external_data_call_fk',
            'phonet_call_external_data',
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
        $this->dropForeignKey('phonet_call_external_data_call_fk', 'phonet_call_external_data');
        $this->dropTable('phonet_call_external_data');
    }
}
