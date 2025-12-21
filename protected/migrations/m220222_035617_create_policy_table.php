<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%policy}}`.
 */
class m220222_035617_create_policy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%policy}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'spa_no' => $this->string(50)->notNull(),
            'policy_no' => $this->string(50),
            'partner_id' => $this->integer()->notNull(),
            'spa_date' => $this->date()->notNull(),
            'distribution_channel' => $this->string(50)->notNull(),
            'pic_name' => $this->string(),
            'pic_title' => $this->string(),
            'pic_id_card_no' => $this->string(50),
            'pic_phone' => $this->string(50),
            'pic_email' => $this->string(50),
            'bank_id' => $this->integer()->notNull(),
            'bank_branch' => $this->string()->notNull(),
            'bank_account_no' => $this->string()->notNull(),
            'bank_account_name' => $this->string()->notNull(),
            'payment_method' => $this->string(50)->notNull(),
            'effective_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'insurance_period' => $this->integer()->notNull(),
            'payment_period' => $this->integer()->notNull(),
            'member_type' => $this->string(50)->notNull(),
            'member_qty' => $this->integer()->notNull(),
            'member_insured' => $this->integer()->notNull(),
            'notes' => $this->string(),
            'work_location' => $this->string(),
            'sign_location' => $this->string(),
            'sign_date' => $this->date(),
            'sign_by' => $this->string(),
            'sign_title' => $this->string(),
            'spa_status' => $this->string(50)->notNull()->defaultValue('Register'),
            'status' => $this->string(50)->notNull()->defaultValue('New'),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
            'issued_at' => $this->dateTime(),
            'issued_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%policy}}');
    }
}
