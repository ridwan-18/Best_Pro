<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%personal}}`.
 */
class m230724_043759_add_profession_column_to_personal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('personal', 'profession', $this->string()->after('city'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('personal', 'profession');
    }
}
