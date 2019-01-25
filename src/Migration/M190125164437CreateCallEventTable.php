<?php

namespace Wearesho\Phonet\Yii\Migration;

use yii\db\Migration;

/**
 * Class M190125164437CreateCallEventTable
 */
class M190125164437CreateCallEventTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('phonet_call_event', [

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('phonet_call_event');
    }
}
