<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_log}}`.
 */
class m250602_041156_create_request_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%request_log}}', [
            'id' => $this->primaryKey(),
            'method' => $this->string(10),
            'url' => $this->string(),
            'ip' => $this->string(45),
            'user_id' => $this->integer(),
            'params' => $this->text(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%request_log}}');
    }
}
