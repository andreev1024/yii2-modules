<?php

use yii\db\Schema;
use jamband\schemadump\Migration;

class m151101_103006_create_errors_table extends Migration
{
    public $tableName = '{{%sqs_error}}';

    protected $tableSchema;

    public function init()
    {
        parent::init();
        $this->tableSchema = $this->db->schema->getTableSchema($this->tableName, true);
    }

    public function safeUp()
    {
        if ($this->tableSchema) {
            return true;
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'sender_fault' => 'TINYINT(1) NOT NULL',
            'code' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'status' => 'TINYINT(1) NOT NULL DEFAULT 1',
            'metadata' =>  $this->text()->notNull(),
            'queue_url' => $this->string(255)->notNull(),
            'source' =>  $this->text()->notNull(),
            'created_at' => $this->timestamp()->notNull() . ' DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'created_by' => $this->integer() . ' DEFAULT NULL',
            'updated_by' => $this->integer() . ' DEFAULT NULL',
        ], $this->tableOptions);

    }

    public function safeDown()
    {
        if ($this->tableSchema) {
            $this->dropTable($this->tableName);
        }
        return true;
    }
}
