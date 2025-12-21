<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%claim}}`.
 */
class m220510_072639_create_claim_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%claim}}', [
            'id' => $this->primaryKey(),
            'claim_no' => $this->string(50)->notNull(),
            'policy_no' => $this->string(50)->notNull(),
            'member_no' => $this->string(100)->notNull(),
            'claim_age' => $this->integer(),
            'sum_insured_reas' => $this->double()->defaultValue(0),
            'estimated_amount' => $this->double()->defaultValue(0),
            'incident_date' => $this->date()->notNull(),
            'claim_reason' => $this->string()->notNull(),
            'disease' => $this->string()->notNull(),
            'place_of_death' => $this->string()->notNull(),
            'borderaux_claim' => $this->string(),
            'doc_pre_received_date' => $this->date(),
            'doc_received_date' => $this->date(),
            'doc_status' => $this->string(10)->notNull(),
            'doc_complete_date' => $this->date(),
            'doc_notes' => $this->string(),
            'payment_due_date' => $this->date(),
            'payment_amount' => $this->double()->defaultValue(0),
            'claim_amount' => $this->double()->defaultValue(0),
            'cash_value' => $this->double()->defaultValue(0),
            'transfer_type' => $this->string(10),
            'bank_name' => $this->string(),
            'account_no' => $this->string(),
            'account_name' => $this->string(),
            'analyst1_diagnosed_by' => $this->string(),
            'analyst1_diagnose_notes' => $this->string(),
            'analyst1_historical_disease' => $this->string(),
            'analyst1_information' => $this->string(),
            'analyst1_investigation_by_phone' => $this->string(),
            'analyst1_medical_analysis' => $this->string(),
            'analyst1_result1' => $this->string(),
            'analyst1_recommendation1' => $this->string(),
            'analyst1_result2' => $this->string(),
            'analyst1_recommendation2' => $this->string(),
            'dept_approved_by' => $this->string(),
            'dept_approve_notes' => $this->string(),
            'dept_approve_status' => $this->string(15),
            'div_approved_by' => $this->string(),
            'div_approve_notes' => $this->string(),
            'div_approve_status' => $this->string(15),
            'gm_approved_by' => $this->string(),
            'gm_approve_notes' => $this->string(),
            'gm_approve_status' => $this->string(15),
            'dir1_approved_by' => $this->string(),
            'dir1_approve_notes' => $this->string(),
            'dir1_approve_status' => $this->string(15),
            'dir2_approved_by' => $this->string(),
            'dir2_approve_notes' => $this->string(),
            'dir2_approve_status' => $this->string(15),
            'analyst2_diagnosed_by' => $this->string(),
            'analyst2_diagnose_notes' => $this->string(),
            'analyst2_result' => $this->string(),
            'analyst2_recommendation' => $this->string(),
            'dept_process_approved_by' => $this->string(),
            'dept_process_approve_notes' => $this->string(),
            'dept_process_approve_status' => $this->string(15),
            'div_process_approved_by' => $this->string(),
            'div_process_approve_notes' => $this->string(),
            'div_process_approve_status' => $this->string(15),
            'gm_process_approved_by' => $this->string(),
            'gm_process_approve_notes' => $this->string(),
            'gm_process_approve_status' => $this->string(15),
            'dir1_process_approved_by' => $this->string(),
            'dir1_process_approve_notes' => $this->string(),
            'dir1_process_approve_status' => $this->string(15),
            'dir2_process_approved_by' => $this->string(),
            'dir2_process_approve_notes' => $this->string(),
            'dir2_process_approve_status' => $this->string(15),
            'approved_amount' => $this->double()->defaultValue(0),
            'status' => $this->string(20),
            'decision' => $this->string(20),
            'process_date' => $this->date(),
            'remarks' => $this->string(),
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
        $this->dropTable('{{%claim}}');
    }
}
