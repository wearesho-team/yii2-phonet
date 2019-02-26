<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190226094729CreateCallInternalDataTable
 */
class M190226094729CreateCallInternalDataTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('phonet_call_internal_data', [
            'id' => $this->primaryKey(),
            'operator_id' => $this->integer(),
            'call_id' => $this->integer()
        ]);
        $this->addForeignKey(
            'phonet_call_internal_data_operator_fk',
            'phonet_call_internal_data',
            'operator_id',
            'phonet_employee',
            'id'
        );
        $this->addForeignKey(
            'phonet_call_internal_data_call_fk',
            'phonet_call_internal_data',
            'id',
            'phonet_call',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('phonet_call_internal_data_operator_fk', 'phonet_call_internal_data');
        $this->dropForeignKey('phonet_call_internal_data_call_fk', 'phonet_call_internal_data');
        $this->dropTable('phonet_call_internal_data');
    }
}
