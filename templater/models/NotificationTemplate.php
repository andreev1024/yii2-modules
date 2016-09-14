<?php

namespace andreev1024\templater\models;

use Yii;

/**
 * Model for notification-type templates.
 */
class NotificationTemplate extends Template
{
    const TEMPLATE_TYPE = 'notification';

    public $title;

    /**
     * Returns arguments which will be pack into `settings` field.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public function getSettingsArguments()
    {
        return  [
            'title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['title'],  'twigValidate'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'title' => Yii::$app->translate->t('title', 'app'),
        ]);
    }
}