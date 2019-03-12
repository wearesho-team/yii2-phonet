<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190226094729CreateCallInternalTable
 */
class M190226094729CreateCallInternalTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('phonet_call_internal', [
            'id' => $this->primaryKey(),
            'operator_id' => $this->integer()
                ->notNull()
                ->comment('Id of second employee of call'),
            'call_id' => $this->integer()->notNull()
        ]);
        $this->addForeignKey(
            'phonet_call_internal_operator_fk',
            'phonet_call_internal',
            'operator_id',
            'phonet_employee',
            'id'
        );
        $this->addForeignKey(
            'phonet_call_internal_call_fk',
            'phonet_call_internal',
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
        $this->dropForeignKey('phonet_call_internal_operator_fk', 'phonet_call_internal');
        $this->dropForeignKey('phonet_call_internal_call_fk', 'phonet_call_internal');
        $this->dropTable('phonet_call_internal');
    }
}
