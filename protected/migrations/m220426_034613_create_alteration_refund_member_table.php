<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alteration_refund_member}}`.
 */
class m220426_034613_create_alteration_refund_member_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alteration_refund_member}}', [
            'id' => $this->primaryKey(),
            'alteration_no' => $this->string(100),
            'member_no' => $this->string(100),
            'name' => $this->string(),
            'birth_date' => $this->date(),
            'age' => $this->integer(),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'new_end_date' => $this->date(),
            'term' => $this->integer(),
            'remaining_term' => $this->integer(),
            'sum_insured' => $this->double(),
            'premi' => $this->double(),
            'extra_premi' => $this->double(),
            'premi_refund' => $this->double(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alteration_refund_member}}');
    }
}
