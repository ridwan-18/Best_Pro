<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation}}`.
 */
class m220215_063319_create_quotation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation}}', [
            'id' => $this->primaryKey(),
            'proposal_no' => $this->string(50)->notNull(),
            'policy_id' => $this->integer(),
            'partner_id' => $this->integer()->notNull(),
            'member_type' => $this->string(50)->notNull(),
            'member_qty' => $this->integer()->notNull(),
            'last_insurance' => $this->string(),
            'business_type' => $this->string(50)->notNull(),
            'proposed_date' => $this->date()->notNull(),
            'expired_date' => $this->date()->notNull(),
            'term' => $this->string(50)->notNull(),
            'member_card' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'certificate_card' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'distribution_channel' => $this->string(50)->notNull(),
            'agent_id' => $this->integer()->notNull(),
            'payment_method' => $this->string(50)->notNull(),
            'min_age' => $this->integer()->notNull(),
            'max_age' => $this->integer()->notNull(),
            'age_calculate' => $this->string(50)->notNull(),
            'effective_policy' => $this->string(50)->notNull(),
            'rate_type' => $this->string(50)->notNull(),
            'notes' => $this->string(),
            'status' => $this->string(50)->notNull()->defaultValue('New'),
            'is_req_new_rate' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'is_req_tc' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'is_req_reas' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
            'verified_at' => $this->dateTime(),
            'verified_by' => $this->integer(),
            'closed_at' => $this->dateTime(),
            'closed_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quotation}}');
    }
}
