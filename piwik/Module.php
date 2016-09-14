<?php

namespace andreev1024\piwik;

use Yii;

/**
 * Piwik module
 * @author Andreev <andreev1024@gmail.com>
 * @since 2015-07-09
 */
class Module extends \yii\base\Module
{
    public $translateCategory = 'piwik';

    /**
     * @var string
     */
    public $settingsFile = 'settings.json';

    /**
     * Return settings directoty.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public static function getSettingsDir()
    {
        return Yii::getAlias('@uploads') . '/piwik';
    }

    /**
     * Return settings file path
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public static function getSettingsFile()
    {
        return static::getSettingsDir() . '/settings.json';
    }

    /**
     * Return path to code file by scope.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public static function getFileByScope($scope)
    {
        return static::getSettingsDir() . "/{$scope}.php";
    }
}
