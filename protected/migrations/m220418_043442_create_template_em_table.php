<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%template_em}}`.
 */
class m220418_043442_create_template_em_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%template_em}}', [
            'id' => $this->primaryKey(),
            'percentage' => $this->double()->notNull(),
            'age' => $this->smallInteger()->notNull(),
            'em1' => $this->double()->notNull(),
            'em2' => $this->double()->notNull(),
            'em3' => $this->double()->notNull(),
            'em4' => $this->double()->notNull(),
            'em5' => $this->double()->notNull(),
            'em6' => $this->double()->notNull(),
            'em7' => $this->double()->notNull(),
            'em8' => $this->double()->notNull(),
            'em9' => $this->double()->notNull(),
            'em10' => $this->double()->notNull(),
            'em11' => $this->double()->notNull(),
            'em12' => $this->double()->notNull(),
            'em13' => $this->double()->notNull(),
            'em14' => $this->double()->notNull(),
            'em15' => $this->double()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%template_em}}');
    }
}
