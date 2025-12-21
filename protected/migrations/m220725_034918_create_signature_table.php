<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%signature}}`.
 */
class m220725_034918_create_signature_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%signature}}', [
            'id' => $this->primaryKey(),
            'policy_name' => $this->string()->notNull(),
            'policy_position' => $this->string()->notNull(),
            'policy_picture' => $this->string()->notNull(),
            'member_name' => $this->string()->notNull(),
            'member_position' => $this->string()->notNull(),
            'member_picture' => $this->string()->notNull(),
            'claim_name' => $this->string()->notNull(),
            'claim_position' => $this->string()->notNull(),
            'claim_picture' => $this->string()->notNull(),
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
        $this->dropTable('{{%signature}}');
    }
}
