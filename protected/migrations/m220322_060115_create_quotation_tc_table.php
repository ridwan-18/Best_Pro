<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation_tc}}`.
 */
class m220322_060115_create_quotation_tc_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_tc}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'min_age' => $this->integer()->notNull(),
            'max_age' => $this->integer()->notNull(),
            'age_term' => $this->integer()->notNull(),
            'max_term' => $this->integer()->notNull(),
            'retroactive' => $this->integer()->notNull(),
            'max_si' => $this->double()->notNull(),
            'min_premi' => $this->double()->notNull(),
            'max_premi' => $this->double()->notNull(),
            'maturity_age' => $this->integer()->notNull(),
            'rate_em' => $this->string(50)->notNull(),
            'refund_premium' => $this->double()->notNull(),
            'refund_type' => $this->string(50)->notNull(),
            'refund_doc' => $this->integer()->notNull(),
            'grace_period' => $this->integer()->notNull(),
            'grace_type' => $this->string(50)->notNull(),
            'claim_doc' => $this->integer()->notNull(),
            'claim_ratio' => $this->double()->notNull(),
            'claim_type' => $this->string(50)->notNull(),
            'administration_cost' => $this->double()->notNull(),
            'policy_cost' => $this->double()->notNull(),
            'member_card_cost' => $this->double()->notNull(),
            'certificate_cost' => $this->double()->notNull(),
            'stamp_cost' => $this->double()->notNull(),
            'medical_checkup' => $this->string(50)->notNull(),
            'remarks' => $this->string(),
            'release_date' => $this->date()->notNull(),
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
        $this->dropTable('{{%quotation_tc}}');
    }
}
