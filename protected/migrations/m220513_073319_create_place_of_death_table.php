<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%place_of_death}}`.
 */
class m220513_073319_create_place_of_death_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%place_of_death}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%place_of_death}}');
    }
}
