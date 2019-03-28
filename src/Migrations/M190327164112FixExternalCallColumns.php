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
        if ($this->getDb()->getDriverName() === 'pgsql') {
            $type = 'DROP NOT NULL';
        } else {
            $type = $this->string();
        }

        $this->alterColumn('phonet_call_external', 'trunk_name', $type);
        $this->alterColumn('phonet_call_external', 'trunk_number', $type);
    }
}
