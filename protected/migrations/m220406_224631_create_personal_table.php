<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%personal}}`.
 */
class m220406_224631_create_personal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%personal}}', [
            'id' => $this->primaryKey(),
            'personal_no' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'birth_place' => $this->string(),
            'birth_date' => $this->date()->notNull(),
            'gender' => $this->string(20),
            'id_card_no' => $this->string(),
            'phone' => $this->string(),
            'email' => $this->string(),
            'address' => $this->string(),
            'province' => $this->string(),
            'city' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%personal}}');
    }
}
