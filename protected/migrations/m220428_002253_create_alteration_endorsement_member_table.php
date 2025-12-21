<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alteration_endorsement_member}}`.
 */
class m220428_002253_create_alteration_endorsement_member_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alteration_endorsement_member}}', [
            'id' => $this->primaryKey(),
            'alteration_no' => $this->string(100),
            'member_no' => $this->string(100),
            'name' => $this->string(),
            'birth_date' => $this->date(),
            'new_birth_date' => $this->date(),
            'age' => $this->integer(),
            'new_age' => $this->integer(),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'new_start_date' => $this->date(),
            'new_end_date' => $this->date(),
            'term' => $this->integer(),
            'new_term' => $this->integer(),
            'sum_insured' => $this->double(),
            'new_sum_insured' => $this->double(),
            'premi' => $this->double(),
            'new_premi' => $this->double(),
            'extra_premi' => $this->double(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alteration_endorsement_member}}');
    }
}
