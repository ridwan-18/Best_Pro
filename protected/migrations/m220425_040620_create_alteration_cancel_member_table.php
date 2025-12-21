<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alteration_cancel_member}}`.
 */
class m220425_040620_create_alteration_cancel_member_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%alteration_cancel_member}}', [
            'id' => $this->primaryKey(),
            'alteration_no' => $this->string(100),
            'member_no' => $this->string(100),
            'name' => $this->string(),
            'birth_date' => $this->date(),
            'age' => $this->integer(),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'sum_insured' => $this->double(),
            'premi' => $this->double(),
            'extra_premi' => $this->double(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alteration_cancel_member}}');
    }
}
