<?php

namespace andreev1024\rbac;

class Module extends \yii\base\Module
{
    /**
     * translate category for i18n
     * @var string
     */
    public $translateCategory = 'rbac';

    public $controllerNamespace = 'andreev1024\rbac\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
