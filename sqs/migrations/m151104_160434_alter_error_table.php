<?php

use yii\db\Schema;
use jamband\schemadump\Migration;

class m151104_160434_alter_error_table extends Migration
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
            $this->addColumn($this->tableName, 'type', 'TINYINT(3) NOT NULL');
            $this->renameColumn($this->tableName, 'message', 'error_message');
            $this->renameColumn($this->tableName, 'source', 'message');
        }
    }

    public function safeDown()
    {
        if ($this->tableSchema) {
            $this->dropColumn($this->tableName, 'type');
            $this->renameColumn($this->tableName, 'message', 'source');
            $this->renameColumn($this->tableName, 'error_message', 'message');
        }
    }
}
