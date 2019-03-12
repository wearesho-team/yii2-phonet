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
                ->comment('Unique id of employee in your cms system'),
            'internal_number' => $this->string()
                ->notNull()
                ->comment('Internal number of employee'),
            'display_name' => $this->string()
                ->notNull()
                ->comment('Name of employee'),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('phonet_employee');
    }
}
