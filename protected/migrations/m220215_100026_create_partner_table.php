<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner}}`.
 */
class m220215_100026_create_partner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'province' => $this->string(),
            'city' => $this->string(),
            'address' => $this->string(),
            'zip_code' => $this->string(50),
            'phone' => $this->string(50),
            'fax' => $this->string(50),
            'email' => $this->string(),
            'established_date' => $this->date(),
            'npwp' => $this->string(100),
            'certificate_no' => $this->string(50),
            'siup' => $this->string(100),
            'business_type' => $this->string(50),
            'fund_source' => $this->string(),
            'insurance_purpose' => $this->string(),
            'insurance_purpose_description' => $this->string(),
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
        $this->dropTable('{{%partner}}');
    }
}
