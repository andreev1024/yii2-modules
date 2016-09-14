<?php

namespace andreev1024\sqs\models;

use andreev1024\sqs\Module;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Model for Sqs errors.
 * @package andreev1024\sqs
 */
class SqsError extends ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_FIXED = 2;

    const SCENARIO_SEARCH = 'search';

    const TYPE_SEND_ERROR = 1;
    const TYPE_DEADLETTERS = 2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer', 'on' => self::SCENARIO_SEARCH],
            [['type'], 'required'],
            [['type'], 'in', 'range' => array_keys(static::getTypeArray())],
            ['sender_fault', 'boolean'],
            [['code', 'error_message', 'metadata', 'message', 'queue_url'], 'string'],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => array_keys(static::getStatusArray())]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::$app->translate->t('type', Module::$translateCategory),
            'sender_fault' => Yii::$app->translate->t('sender fault', Module::$translateCategory),
            'queue_url' => Yii::$app->translate->t('queue url', Module::$translateCategory),
            'code' => Yii::$app->translate->t('code', Module::$translateCategory),
            'error_message' => Yii::$app->translate->t('error message', Module::$translateCategory),
            'status' => Yii::$app->translate->t('status', Module::$translateCategory),
            'metadata' => Yii::$app->translate->t('metadata', Module::$translateCategory),
            'message' => Yii::$app->translate->t('message', Module::$translateCategory),
            'created_at' => Yii::$app->translate->t('created at', Module::$translateCategory),
            'updated_at' => Yii::$app->translate->t('updated at', Module::$translateCategory),
            'created_by' => Yii::$app->translate->t('created by', Module::$translateCategory),
            'updated_by' => Yii::$app->translate->t('updated by', Module::$translateCategory),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEARCH] = ['id', 'code', 'queue_url', 'type'];
        return $scenarios;
    }

    /**
     * Return array with statuses.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_NEW => Yii::$app->translate->t('new', Module::$translateCategory),
            self::STATUS_FIXED => Yii::$app->translate->t('fixed', Module::$translateCategory),
        ];
    }

    /**
     * Return array with types.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getTypeArray()
    {
        return [
            self::TYPE_SEND_ERROR => Yii::$app->translate->t('send error', Module::$translateCategory),
            self::TYPE_DEADLETTERS => Yii::$app->translate->t('deadletters', Module::$translateCategory),
        ];
    }

    /**
     * Search method for current model.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->scenario = self::SCENARIO_SEARCH;
        $query = parent::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'LIKE', 'code', $this->code,
        ]);

        $query->andFilterWhere([
            'LIKE', 'queue_url', $this->queue_url,
        ]);

        $query->andFilterWhere([
            'IN', 'id', $this->id,
        ]);

        $query->andFilterWhere([
            'type' => $this->type
        ]);

        return $dataProvider;
    }

    public function saveDeadletterByApi()
    {

    }

    public function saveByApi()
    {

    }
}
