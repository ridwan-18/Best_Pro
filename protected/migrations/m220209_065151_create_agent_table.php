<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%agent}}`.
 */
class m220209_065151_create_agent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%agent}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull(),
            'name' => $this->string()->notNull(),
            'id_card_number' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
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
        $this->dropTable('{{%agent}}');
    }
}
