<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 */
class m231111_140511_create_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'path' => $this->string(255),
            'day' => $this->string(50),
            'size' => $this->integer(),
            'type' => $this->string(255)->comment('file type: pdf, zip'),
            'status' => $this->integer(),
            'is_main' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->createIndex(
            'idx-file-is_main',
            'file',
            'is_main'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%file}}');
    }
}
