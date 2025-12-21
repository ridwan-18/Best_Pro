<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%claim_document}}`.
 */
class m220517_095019_create_claim_document_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%claim_document}}', [
            'id' => $this->primaryKey(),
            'claim_id' => $this->integer()->notNull(),
            'document_id' => $this->integer()->notNull(),
            'is_checked' => $this->integer(),
            'is_mandatory' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%claim_document}}');
    }
}
