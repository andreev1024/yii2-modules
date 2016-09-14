<?php

namespace andreev1024\piwik\models;

use andreev1024\piwik\Module;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

/**
 * TrackingCode Model. This model save and use data from JSON file.
 */
class TrackingCode extends Model
{
    /**
     * backend scope.
     */
    const SCOPE_BACKEND = 'backend';

    /**
     * frontend scope.
     */
    const SCOPE_FRONTEND = 'frontend';

    const SCENARIO_CREATE = 'create';

    const SCENARIO_UPDATE = 'update';

    /**
     * Event which is fired after model save.
     */
    const EVENT_AFTER_SAVE = 'afterSave';

    public $status;
    public $trackVisitorsAcrossAllSubdomains;
    public $prependTitle;
    public $notCountedAliasLink;
    public $disableCookies;
    public $imageTracking;
    public $scope;
    public $trackerUrl;
    public $siteId;
    public $mainSiteDomen;

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_SAVE, [$this, 'afterSave']);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = [
            'status',
            'trackerUrl',
            'siteId',
            'trackVisitorsAcrossAllSubdomains',
            'prependTitle',
            'notCountedAliasLink',
            'disableCookies',
            'imageTracking',
            'mainSiteDomen',
        ];

        $scenarios[self::SCENARIO_CREATE] = [
            'status',
            'trackerUrl',
            'siteId',
            'trackVisitorsAcrossAllSubdomains',
            'prependTitle',
            'notCountedAliasLink',
            'disableCookies',
            'imageTracking',
            'mainSiteDomen',
            'scope',
        ];

        return $scenarios;
    }

    /**
     * Model rules.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public function rules()
    {
        return [
            [['trackerUrl', 'siteId'], 'required'],
            [['trackerUrl'], 'match', 'pattern' => '#^[-a-zA-z0-9\.]+$#i'],
            [
                'mainSiteDomen',
                'required',
                'when' => function($model) {
                    return $this->trackVisitorsAcrossAllSubdomains || $this->notCountedAliasLink;
                },
                'message' => Yii::$app->translate->t('For current configuration field cannot be blank')
            ],
            [['scope'], 'in', 'range' => array_keys(static::getScopesArray())],
            ['scope', 'validateScope', 'on' => self::SCENARIO_CREATE],
            [['mainSiteDomen'], 'match', 'pattern' => '#^[-a-zA-z0-9]+\.[-a-zA-z]+$#i'],
            ['siteId', 'integer'],
            [
                [
                    'status', 'trackVisitorsAcrossAllSubdomains', 'prependTitle',
                    'notCountedAliasLink', 'disableCookies', 'imageTracking',
                ],
                'integer',
                'min' => 0,
                'max' => 1,
            ],
        ];
    }

    /**
     * Labels for model attributes.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'status' => Yii::$app->translate->t('enable'),
            'trackerUrl' => Yii::$app->translate->t('tracker url'),
            'siteId' => Yii::$app->translate->t('site Id'),
            'trackVisitorsAcrossAllSubdomains' => Yii::$app->translate->t('track visitors across all subdomains of your main website'),
            'prependTitle' => Yii::$app->translate->t('prepend the site domain to the page title when tracking'),
            'notCountedAliasLink' => Yii::$app->translate->t('in the "Outlinks" report, hide clicks to known alias URLs of your website'),
            'disableCookies' => Yii::$app->translate->t('disable all tracking cookies'),
            'imageTracking' => Yii::$app->translate->t('enable'),
            'mainSiteDomen' => Yii::$app->translate->t('main site domen'),
            'scope' => Yii::$app->translate->t('scope'),
        ];
    }

    /**
     * Hints for model attributes.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param string $name
     *
     * @return mixed
     */
    public static function getHint($name)
    {
        $hints = [
            'status' => '',
            'trackVisitorsAcrossAllSubdomains' => 'so if one visitor visits x.app.net and y.app.net, they will be counted as a unique visitor',
            'prependTitle' => 'So if someone visits the "About" page on blog.app.nwt it will be recorded as "blog / About". This is the easiest way to get an overview of your traffic by sub-domain.',
            'notCountedAliasLink' => 'so clicks on links to Alias URLs (eg. x.domain.com) will not be counted as "Outlink"',
            'disableCookies' => 'disables all first party cookies. Existing Piwik cookies for this website will be deleted on the next page view',
            'imageTracking' => 'When a visitor has disabled JavaScript, or when javaScript cannot be used, an image tracking link can be used to track visitors.'
        ];

        return Yii::$app->translate->t($hints[$name]);
    }

    /**
     * Return array with scopes.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getScopesArray()
    {
        return [
            self::SCOPE_BACKEND => Yii::$app->translate->t(self::SCOPE_BACKEND),
            self::SCOPE_FRONTEND => Yii::$app->translate->t(self::SCOPE_FRONTEND),
        ];
    }

    public function validateScope($attribute, $params)
    {
        $settings = static::getSettings();
        if(isset($settings[$this->$attribute])) {
            $this->addError($attribute, Yii::$app->translate->t('Code for this scope is exist.'));
        }
    }

    /**
     * Save model.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param bool $validate
     *
     * @return int
     */
    public function save($validate = true)
    {
        $dataIsValid = true;
        if ($validate) {
            $dataIsValid = $this->validate();
        }

        if ($dataIsValid) {
            $settings = static::getSettings();
            $settings[$this->scope] = $this->attributes;
            $result = file_put_contents(Module::getSettingsFile(), Json::encode($settings));
            if ($result) {
                $this->trigger(self::EVENT_AFTER_SAVE);
            }
            return $result;
        }
    }

    /**
     * Return model by scope and siteId.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param string $scope
     * @param null $default
     *
     * @return mixed|null|TrackingCode
     */
    public static function findOne($scope, $default = null)
    {
        $data = static::getSettings();
        $data = isset($data[$scope]) ? $data[$scope] : $default;
        if ($data) {
            $data = new self($data);
        }
        return $data;
    }

    /**
     * Return settings as array.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param array $default
     *
     * @return array
     */
    public static function getSettings($default = [])
    {
        $file = Module::getSettingsFile();
        if (!file_exists(Module::getSettingsDir())) {
            mkdir(Module::getSettingsDir());
        }

        if (!file_exists($file)) {
            return $default;
        }

        $settings = Json::decode(file_get_contents($file));
        return $settings ? : $default;
    }

    /**
     * Return reindex settings.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getSettingsList()
    {
        return array_values(static::getSettings());
    }

    /**
     * Delete model settings.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $scope
     * @param $siteId
     *
     * @return int
     */
    public static function delete($scope)
    {
        $settings = static::getSettings();
        unset($settings[$scope]);
        $file = Module::getInstance()->getSettingsFile();
        return file_put_contents($file, Json::encode($settings));
    }

    /**
     * Render piwik code and save in runtime directory.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $event
     *
     * @return int
     */
    public function afterSave($event)
    {
        $controller = Yii::$app->controller;
        $code = $this->status ? $controller->renderPartial('_code', ['model'=>$this]) : '';
        $scope = $this['scope'];
        $file = Module::getFileByScope($scope);
        return file_put_contents($file, $code);
    }
}
