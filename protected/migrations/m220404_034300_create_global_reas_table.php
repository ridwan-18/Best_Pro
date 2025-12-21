<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%global_reas}}`.
 */
class m220404_034300_create_global_reas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%global_reas}}', [
            'id' => $this->primaryKey(),
            'reassuradur_id' => $this->string()->notNull(),
            'pks_no' => $this->string()->notNull(),
            'effective_date' => $this->date()->notNull(),
            'expired_date' => $this->date()->notNull(),
            'is_unlimited' => $this->integer(),
            'reas_type' => $this->string(20)->notNull(),
            'reas_method' => $this->string(20)->notNull(),
            'ceding_share' => $this->double()->notNull(),
            'reas_share' => $this->double()->notNull(),
            'rate_period' => $this->string(20)->notNull(),
            'rate_type' => $this->string(20)->notNull(),
            'prorate_type' => $this->string(20)->notNull(),
            'cover_note' => $this->string()->notNull(),
            'retroactive' => $this->integer()->notNull(),
            'claim_expired' => $this->integer()->notNull(),
            'commission' => $this->double()->notNull(),
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
        $this->dropTable('{{%global_reas}}');
    }
}
