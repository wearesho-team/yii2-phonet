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
            'user_id' => $this->integer()
                ->unsigned()
                ->null()
                ->comment('Unique id of employee of your cms system'),
            'internal_number' => $this->string()
                ->notNull()
                ->comment('Internal number'),
            'display_name' => $this->string()->notNull(),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('phonet_employee');
    }
}
