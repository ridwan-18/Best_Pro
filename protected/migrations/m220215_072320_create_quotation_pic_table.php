<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation_pic}}`.
 */
class m220215_072320_create_quotation_pic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_pic}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'job_position' => $this->string()->notNull(),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quotation_pic}}');
    }
}
