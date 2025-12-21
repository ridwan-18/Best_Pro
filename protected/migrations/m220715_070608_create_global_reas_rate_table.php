<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%global_reas_rate}}`.
 */
class m220715_070608_create_global_reas_rate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%global_reas_rate}}', [
            'id' => $this->primaryKey(),
            'global_reas_id' => $this->integer()->notNull(),
            'age' => $this->integer(),
            'term' => $this->integer()->notNull(),
            'rate' => $this->double()->notNull(),
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
        $this->dropTable('{{%global_reas_rate}}');
    }
}
