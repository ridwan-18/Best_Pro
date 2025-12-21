<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%disease}}`.
 */
class m220513_073307_create_disease_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%disease}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%disease}}');
    }
}
