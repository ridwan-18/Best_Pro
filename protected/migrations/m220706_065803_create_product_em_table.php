<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_em}}`.
 */
class m220706_065803_create_product_em_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_em}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'age' => $this->smallInteger()->notNull(),
            'percentage' => $this->double()->notNull(),
            'term' => $this->smallInteger()->notNull(),
            'em' => $this->double()->notNull(),
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
        $this->dropTable('{{%product_em}}');
    }
}
