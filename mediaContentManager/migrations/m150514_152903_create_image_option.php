<?php

use yii\db\Schema;

class m150514_152903_create_image_option extends \yii\db\Migration
{
    /** @var string */
    protected $tableName = '{{%image_option}}';

    public function safeUp()
    {
        if ($this->db->getTableSchema($this->tableName) !== null) {
            $this->dropTable($this->tableName);
        }

        $this->createTable($this->tableName, [
            'type' => Schema::TYPE_STRING . '(20) NOT NULL',
            'width' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'height' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'PRIMARY KEY (type)',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
