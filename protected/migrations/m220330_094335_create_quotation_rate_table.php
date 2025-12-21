<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation_rate}}`.
 */
class m220330_094335_create_quotation_rate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_rate}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'type' => $this->string(),
            'age' => $this->integer(),
            'term' => $this->integer(),
            'unit' => $this->integer(),
            'rate' => $this->double(),
            'interest' => $this->double(),
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
        $this->dropTable('{{%quotation_rate}}');
    }
}
