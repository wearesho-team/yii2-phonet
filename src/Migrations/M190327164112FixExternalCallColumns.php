<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190327164112FixExternalCallColumns
 */
class M190327164112FixExternalCallColumns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('phonet_call_external', 'trunk_name', 'DROP NOT NULL');
        $this->alterColumn('phonet_call_external', 'trunk_number', 'DROP NOT NULL');
    }
}
