<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%billing}}`.
 */
class m220421_041113_create_billing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%billing}}', [
            'id' => $this->primaryKey(),
            'batch_no' => $this->string(50)->notNull(),
            'policy_no' => $this->string(50)->notNull(),
            'reg_no' => $this->string(50)->notNull(),
            'invoice_no' => $this->string(50)->notNull(),
            'invoice_date' => $this->date()->notNull(),
            'due_date' => $this->date()->notNull(),
            'accept_date' => $this->date()->notNull(),
            'total_member' => $this->integer()->notNull()->defaultValue(0),
            'gross_premium' => $this->double()->notNull()->defaultValue(0),
            'extra_premium' => $this->double()->notNull()->defaultValue(0),
            'discount' => $this->double()->notNull()->defaultValue(0),
            'handling_fee' => $this->double()->notNull()->defaultValue(0),
            'pph' => $this->double()->notNull()->defaultValue(0),
            'ppn' => $this->double()->notNull()->defaultValue(0),
            'nett_premium' => $this->double()->notNull()->defaultValue(0),
            'admin_cost' => $this->double()->notNull()->defaultValue(0),
            'policy_cost' => $this->double()->notNull()->defaultValue(0),
            'member_card_cost' => $this->double()->notNull()->defaultValue(0),
            'certificate_cost' => $this->double()->notNull()->defaultValue(0),
            'stamp_cost' => $this->double()->notNull()->defaultValue(0),
            'total_billing' => $this->double()->notNull()->defaultValue(0),
            'memo_no' => $this->string(50),
            'memo_date' => $this->date(),
            'print_date' => $this->date(),
            'payment_date' => $this->date(),
            'remarks' => $this->string(),
            'is_checked_by_uw' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_checked_by_finance' => $this->smallInteger()->notNull()->defaultValue(0),
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
        $this->dropTable('{{%billing}}');
    }
}
