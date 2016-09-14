<?php
namespace andreev1024\mediaContentManager\widgets\assets;

use yii\web\AssetBundle;

class FileManagerAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = dirname(__FILE__);
        $this->js = ['js/fileManager.js'];
        $this->css = ['css/fileManager.css'];
        $this->depends = [
            'yii\web\JqueryAsset',
            'yii\bootstrap\BootstrapAsset',
        ];
    }
}
