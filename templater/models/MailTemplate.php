<?php

namespace andreev1024\templater\models;

use Yii;

/**
 * Model for mail-type templates.
 */
class MailTemplate extends Template
{
    const TEMPLATE_TYPE = 'mail';

    const TYPE_HTML = 1;
    const TYPE_PLAIN = 0;

    public $type;
    public $subject;

    /**
     * Returns arguments which will be pack into `settings` field.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public function getSettingsArguments()
    {
        return  [
            'type',
            'subject'
        ];
    }

    /**
     * @return array
     */
    public static function getMailType()
    {
        return [
            static::TYPE_PLAIN => Yii::$app->translate->t('plain text', 'app'),
            static::TYPE_HTML => Yii::$app->translate->t('html', 'app'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['subject'], 'required'],
            [['template'], 'isPlain', 'when' => function ($model) {
                return $model->type == self::TYPE_PLAIN;
            }],
            [['type'], 'in', 'range' => array_keys(static::getMailType())],
            [['subject'],  'string', 'max' => 255],
            [['subject'],  'twigValidate'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type' => Yii::$app->translate->t('type', 'app'),
            'subject' => Yii::$app->translate->t('subject', 'app'),
        ]);
    }

    /**
     * @param $attribute
     *
     * @return bool
     */
    public function isPlain($attribute)
    {
        $result = preg_match("/<[^<]+>/", $this->$attribute) == 0;

        if (!$result) {
            $this->addError($attribute, $attribute . ' is not plain');
        }

        return $result;
    }
}