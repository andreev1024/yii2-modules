<?php

namespace andreev1024\recron\models;

use yii;
use yii\db\ActiveRecord;

/**
 * Class model Cron
 */
class Cron extends ActiveRecord
{
    public static function tableName()
    {
        return 're_cron';
    }

    public function rules()
    {
        return [
            [['minutes', 'hours', 'days', 'months', 'week', 'command', 'active'], 'required'],
            [['minutes', 'hours', 'days', 'months', 'week'], 'default', 'value' => '*'],
            [['active'], 'default', 'value' => 1],
            [['active'], 'boolean'],
            [['minutes', 'hours', 'days', 'months', 'week'], 'string', 'max' => 16],
            [['command'], 'string', 'max' => 255],
            [['minutes', 'hours', 'days', 'months', 'week'], 'match', 'pattern' => '/[,\-*\d]+/'],
        ];
    }
}
