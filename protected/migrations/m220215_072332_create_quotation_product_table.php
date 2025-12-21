<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation_product}}`.
 */
class m220215_072332_create_quotation_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_product}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'premium_type' => $this->string(50)->notNull(),
            'rate_type' => $this->string(50)->notNull(),
            'period_type' => $this->string(50)->notNull(),
            'si_type' => $this->string(50)->notNull(),
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
        $this->dropTable('{{%quotation_product}}');
    }
}
