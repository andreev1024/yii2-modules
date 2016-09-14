<?php
namespace andreev1024\recron\controllers;

use yii;
use yii\console\Controller;
use andreev1024\recron\models\Cron;

class ConsoleController extends Controller {
    /**
     * Print current cron table
     */
    public function actionTable($crontabFile){
        $cronModel = Cron::findAll(['active' => 1]);
        $cronTab = [];
        foreach ($cronModel as $item){
            $cronTab[] = $item->minutes.' '.$item->hours.' '.$item->days.' '.$item->months.' '.$item->week.' '.$item->command;
        }
        
        if (!is_writable($crontabFile))
            return parent::EXIT_CODE_ERROR;

        sleep(1);
        file_put_contents($crontabFile, implode(PHP_EOL, $cronTab).PHP_EOL);
        
        return parent::EXIT_CODE_NORMAL;
    }
}
