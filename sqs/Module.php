<?php

namespace andreev1024\sqs;

/**
 * Module class for SQS component.
 * @package andreev1024\sqs
 */
class Module extends \yii\base\Module
{
    /**
     * translate category for i18n
     * @var string
     */
    public static $translateCategory = 'sqs';

    public $defaultRoute = 'error';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
