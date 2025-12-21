<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%claim_reason}}`.
 */
class m220513_073257_create_claim_reason_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%claim_reason}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%claim_reason}}');
    }
}
