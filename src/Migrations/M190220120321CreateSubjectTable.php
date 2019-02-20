<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190220120321CreateSubjectTable
 */
class M190220120321CreateSubjectTable extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('phonet_subject', [
            'id' => $this->primaryKey(),
            'number' => $this->string(),
            'uri' => $this->string(),
            'internal_id' => $this->string()->null(),
            'name' => $this->string()->null(),
            'company' => $this->string()->null(),
            'priority' => $this->string()->null(),
            'call_event_id' => $this->integer(),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('phonet_subject');
    }
}
