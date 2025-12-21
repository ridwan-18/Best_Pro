<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%global_reas_uw_limit}}`.
 */
class m220715_070616_create_global_reas_uw_limit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%global_reas_uw_limit}}', [
            'id' => $this->primaryKey(),
            'global_reas_id' => $this->integer()->notNull(),
            'min_si' => $this->double()->notNull(),
            'max_si' => $this->double()->notNull(),
            'min_age' => $this->integer()->notNull(),
            'max_age' => $this->integer()->notNull(),
            'medical_code' => $this->string(50)->notNull(),
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
        $this->dropTable('{{%global_reas_uw_limit}}');
    }
}
