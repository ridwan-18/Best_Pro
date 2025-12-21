<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%reassuradur}}`.
 */
class m220331_083422_create_reassuradur_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%reassuradur}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'phone' => $this->string(),
            'fax' => $this->string(),
            'email' => $this->string(),
            'address' => $this->string(),
            'postal_code' => $this->string(),
            'city' => $this->string(),
            'established_year' => $this->integer(),
            'tax_payer_identification' => $this->string(),
            'trade_business_license' => $this->string(),
            'company_deed' => $this->string(),
            'pic_name' => $this->string(),
            'payment_due_date' => $this->integer(),
            'bank_name' => $this->string(),
            'bank_branch' => $this->string(),
            'bank_account_name' => $this->string(),
            'bank_account_number' => $this->string(),
            'payment_bank_name' => $this->string(),
            'payment_bank_branch' => $this->string(),
            'payment_bank_account_name' => $this->string(),
            'payment_bank_account_number' => $this->string(),
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
        $this->dropTable('{{%reassuradur}}');
    }
}
