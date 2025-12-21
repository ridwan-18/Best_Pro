<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m220202_062146_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'email' => $this->string(),
            'phone' => $this->string(),
            'username' => $this->string(30)->notNull(),
            'password' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'auth_key' => $this->string(32),
            'access_token' => $this->string(),
            'role' => $this->smallInteger()->notNull()->defaultValue(1),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
        ]);

        $this->insert('user', [
            'name' => 'Administrator',
            'email' => 'admin@domain.com',
            'phone' => '0867746567452',
            'username' => 'admin',
            'password' => '95b617cf157c32c3988d8b4c92b0d9b8d5206b7aRFzPIVhwjgNMjpTF',
            'role' => 1,
            'status' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
