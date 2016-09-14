<?php
namespace andreev1024\templater;

/**
 * @author Andreev <andreev1024@gmail.com>
 * @since 2015-05-07
 */

class Module extends \yii\base\Module
{
    /**
     * translate category for i18n
     * @var string
     */
    public $translateCategory = 'templator';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
