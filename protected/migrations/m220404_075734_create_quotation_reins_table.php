<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quotation_reins}}`.
 */
class m220404_075734_create_quotation_reins_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quotation_reins}}', [
            'id' => $this->primaryKey(),
            'quotation_id' => $this->integer()->notNull(),
            'global_reas_id' => $this->integer()->notNull(),
            'si_from' => $this->double()->notNull(),
            'si_to' => $this->double()->notNull(),
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
        $this->dropTable('{{%quotation_reins}}');
    }
}
