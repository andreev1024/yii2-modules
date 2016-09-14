<?php

use yii\db\Schema;
use jamband\schemadump\Migration;

class m150507_130004_create_re_template extends Migration
{
    public $tableName = '{{%template}}';

    protected $tableSchema;

    public function init()
    {
        parent::init();
        $this->tableSchema = $this->db->schema->getTableSchema($this->tableName, true);
    }

    public function safeUp()
    {
        if ($this->tableSchema === null) {
            $this->createTable($this->tableName, [
                'id' => Schema::TYPE_PK,
                'entity' => Schema::TYPE_STRING . "(255) NULL",
                'name' => Schema::TYPE_STRING . "(255) NULL",
                'template' => Schema::TYPE_TEXT . " NULL",
                'settings' => Schema::TYPE_TEXT . " NULL",
                'flag_main' =>  "tinyint(1) DEFAULT '0'",
                'created_at' => Schema::TYPE_TIMESTAMP . " NOT NULL DEFAULT CURRENT_TIMESTAMP",
                'updated_at' => Schema::TYPE_TIMESTAMP . " NOT NULL DEFAULT '0000-00-00 00:00:00'",
                'created_by' => Schema::TYPE_INTEGER . "(11) DEFAULT NULL",
                'updated_by' => Schema::TYPE_INTEGER . "(11) DEFAULT NULL"
            ], $this->tableOptions);
        }
    }

    public function safeDown()
    {
        if ($this->tableSchema) {
            $this->dropTable($this->tableName);
        }
        return true;
    }
}
