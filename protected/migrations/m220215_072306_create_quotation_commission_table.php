<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation_commission}}`.
 */
class m220215_072306_create_quotation_commission_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_commission}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'discount' => $this->double(),
            'maintenance_agent_id' => $this->integer(),
            'maintenance_fee' => $this->double(),
            'admin_agent_id' => $this->integer(),
            'admin_fee' => $this->double(),
            'handling_agent_id' => $this->integer(),
            'handling_fee' => $this->double(),
            'pph' => $this->double(),
            'ppn' => $this->double(),
            'refferal_agent_id' => $this->integer(),
            'refferal_fee' => $this->double(),
            'closing_agent_id' => $this->integer(),
            'closing_fee' => $this->double(),
            'fee_based_agent_id' => $this->integer(),
            'fee_based' => $this->double(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quotation_commission}}');
    }
}
