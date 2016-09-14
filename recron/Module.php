<?php

namespace andreev1024\recron;

use yii;
/**
 * Class cron module
 */
class Module extends yii\base\Module implements yii\base\BootstrapInterface {

    /**
     * translate category for i18n
     * @var string
     */
    public $translateCategory = 'shipping';
    
    public function bootstrap($app) {
        if (is_null($app->db->schema->getTableSchema('re_cron', true)))
            $this->createTable();
    }

    private function createTable() {
        yii::$app->db->createCommand()->createTable('re_cron', [
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'minutes' => 'varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'*\'',
            'hours' => 'varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'*\'',
            'days' => 'varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'*\'',
            'months' => 'varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'*\'',
            'week' => 'varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'*\'',
            'command' => 'varchar(255) COLLATE utf8_unicode_ci NOT NULL',
            'active' => 'tinyint(1) NOT NULL DEFAULT \'1\'',
            'PRIMARY KEY (`id`)'
        ], 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;')
        ->execute();
    }
}
