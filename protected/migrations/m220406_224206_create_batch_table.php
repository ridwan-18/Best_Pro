<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%batch}}`.
 */
class m220406_224206_create_batch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%batch}}', [
            'id' => $this->primaryKey(),
            'batch_no' => $this->string(50)->notNull(),
            'policy_no' => $this->string(50)->notNull(),
            'total_member' => $this->integer()->notNull(),
            'total_member_accepted' => $this->integer()->notNull(),
            'total_member_pending' => $this->integer()->notNull(),
            'total_up' => $this->double()->notNull(),
            'total_gross_premium' => $this->double()->notNull(),
            'total_discount_premium' => $this->double()->notNull(),
            'total_extra_premium' => $this->double()->notNull(),
            'total_saving_premium' => $this->double()->notNull(),
            'total_nett_premium' => $this->double()->notNull(),
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
        $this->dropTable('{{%batch}}');
    }
}
