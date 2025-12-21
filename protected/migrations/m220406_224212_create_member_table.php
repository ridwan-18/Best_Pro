<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m220406_224212_create_member_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%member}}', [
            'id' => $this->primaryKey(),
            'policy_no' => $this->string(50)->notNull(),
            'batch_no' => $this->string(50)->notNull(),
            'member_no' => $this->string(100),
            'personal_no' => $this->string()->notNull(),
            'age' => $this->integer(),
            'branch' => $this->string(),
            'branch_code' => $this->string(),
            'account_no' => $this->string(),
            'bank_branch' => $this->string(),
            'branch_code' => $this->string(),
            'term' => $this->integer()->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'sum_insured' => $this->double()->notNull(),
            'total_si' => $this->double(),
            'total_premium' => $this->double(),
            'rate_premi' => $this->double(),
            'rate_saving' => $this->double(),
            'gross_premium' => $this->double(),
            'basic_premium' => $this->double(),
            'saving_premium' => $this->double(),
            'percentage_discount' => $this->double(),
            'discount_premium' => $this->double(),
            'nett_premium' => $this->double(),
            'medical_code' => $this->string(50),
            'status' => $this->string(20),
            'member_status' => $this->string(20),
            'reas_status' => $this->string(20),
            'status_reason' => $this->string(),
            'stnc_date' => $this->date(),
            'stnc_status' => $this->string(),
            'stnc_reason' => $this->string(),
            'acc_status' => $this->string(),
            'percentage_extra_premium' => $this->double(),
            'extra_premium' => $this->double(),
            'em_type' => $this->smallInteger(),
            'percentage_em' => $this->double(),
            'rate_em' => $this->double(),
            'em_premium' => $this->double(),
            'em_notes' => $this->string(),
            'uw_notes' => $this->string(),
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
        $this->dropTable('{{%member}}');
    }
}
