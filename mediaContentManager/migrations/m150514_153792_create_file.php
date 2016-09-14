<?php

use yii\db\Schema;
use yii\db\Migration;

class m150514_153792_create_file extends Migration
{
    /** @var string */
    protected $tableName = '{{%file}}';

    public function safeUp()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        if ($this->db->getTableSchema($this->tableName) !== null) {
            $this->dropTable($this->tableName);
        }

        $this->createTable($this->tableName, [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . "(255) NOT NULL",
            'path' => Schema::TYPE_STRING . "(255) NOT NULL",
            'original_name' => Schema::TYPE_STRING . "(255) NOT NULL",
            'title' => Schema::TYPE_STRING . "(50) NULL",
            'description' => Schema::TYPE_STRING . "(255) NULL",
            'processing_type' => Schema::TYPE_SMALLINT . "(1) UNSIGNED NOT NULL DEFAULT '0'",
            'size' => Schema::TYPE_INTEGER . "(11) NULL",
            'mime_type' => Schema::TYPE_STRING . "(100) NOT NULL",
            'metadata' => Schema::TYPE_TEXT . " NULL",
            'created_by' => Schema::TYPE_INTEGER . "(11) NOT NULL",
            'updated_by' => Schema::TYPE_INTEGER . "(11) NULL",
            'created_at' => Schema::TYPE_TIMESTAMP . " NOT NULL DEFAULT CURRENT_TIMESTAMP",
            'updated_at' => Schema::TYPE_TIMESTAMP . " NULL",
            'storage' => Schema::TYPE_INTEGER . "(11) NOT NULL",
            'storage_directory' => Schema::TYPE_TEXT . " NOT NULL",
            'storage_access' => Schema::TYPE_INTEGER . "(11) NOT NULL"
        ]);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropTable($this->tableName);
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
