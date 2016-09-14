<?php

namespace andreev1024\piwik\widgets;

use andreev1024\piwik\models\TrackingCode;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use andreev1024\piwik\Module;

/**
 * This widget render piwik tracking code. For more deatails see readme file.
 */
class GetCode extends Widget
{
    /**
     * @var string tracking scope (backend/frontend)
     */
    public $scope;

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->scope) {
            throw new InvalidConfigException('Required attribute (scope, siteId) is missed.');
        }

        $allScopes = TrackingCode::getScopesArray();
        if (!isset($allScopes[$this->scope])) {
            throw new InvalidConfigException('Wrong `scope`');
        }
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return mixed|string
     */
    public function run()
    {
        if (!($content = $this->getContent())) {
            return false;
        }

        $replace = [
            '{{title}}' => Yii::$app->controller->route
        ];

        return str_replace(array_keys($replace), array_values($replace), $content);
    }

    /**
     * Return path to tracking code file.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function getContent($default = null)
    {
        $path = Module::getFileByScope($this->scope);
        return file_exists($path) ? file_get_contents($path) : $default;
    }
}
