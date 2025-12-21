<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alteration_cancel}}`.
 */
class m220425_040537_create_alteration_cancel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alteration_cancel}}', [
            'id' => $this->primaryKey(),
            'alteration_no' => $this->string(100)->notNull(),
            'alteration_date' => $this->date()->notNull(),
            'policy_no' => $this->string(50)->notNull(),
            'total_si' => $this->double()->notNull(),
            'total_premium' => $this->double()->notNull(),
            'status' => $this->string(20)->notNull(),
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
        $this->dropTable('{{%alteration_cancel}}');
    }
}
