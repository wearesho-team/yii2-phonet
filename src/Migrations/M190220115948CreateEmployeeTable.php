<?php

namespace Wearesho\Phonet\Yii\Migrations;

use yii\db\Migration;

/**
 * Class M190220115948CreateEmployeeTable
 */
class M190220115948CreateEmployeeTable extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('phonet_employee', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'internal_number' => $this->string(),
            'display_name' => $this->string(),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('phonet_employee');
    }
}
