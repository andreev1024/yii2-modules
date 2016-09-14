<?php

namespace andreev1024\templater\models;

use andreev1024\templater\components\Config;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class Template extends ActiveRecord
{
    const FLAG_MAIN = 1;
    const FLAG_NONE = 0;

    public $skipBeforeSave = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%template}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity', 'name', 'template'], 'required'],
            [['template'], 'string'],
            [['template'], 'twigValidate'],
            [['entity', 'name'], 'string', 'max' => 255],
            [['entity'], 'in', 'range' => array_keys(Config::getEntities())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->translate->t('id', 'app'),
            'entity' => Yii::$app->translate->t('entity', 'app'),
            'name' => Yii::$app->translate->t('name', 'app'),
            'template' => Yii::$app->translate->t('template', 'app'),
            'created_at' => Yii::$app->translate->t('created at', 'app'),
            'updated_at' => Yii::$app->translate->t('updated at', 'app'),
            'created_by' => Yii::$app->translate->t('created by', 'app'),
            'updated_by' => Yii::$app->translate->t('updated by', 'app'),
            'flag_main' => Yii::$app->translate->t('main', 'app'),
        ];
    }

    /**
     * Twig-template validator.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $attribute
     */
    public function twigValidate($attribute)
    {
        $value = $this->$attribute;

        try {
            Yii::$app->twig->getInstance()->parse(Yii::$app->twig->getInstance()->tokenize($value));
        } catch (\Twig_Error_Syntax $e) {
            $this->addError($attribute, $e->getMessage());
        }
    }

    /**
     * Returns entity labels by type.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $type
     *
     * @return array
     */
    public static function getEntityLabelsByType($type)
    {
        $labels = [];
        if ($entities = Config::getEntityByType($type)) {
            $entities = array_keys($entities);
            $labels = array_combine($entities, $entities);
        }

        return $labels;
    }

    /**
     * Returns entity labels.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getEntityLabels()
    {
        $labels = [];
        if ($entities = Config::getEntities()) {
            $entities = array_keys($entities);
            $labels = array_combine($entities, $entities);
        }
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->skipBeforeSave) {
            return true;
        }

        if (parent::beforeSave($insert)) {

            if ($this->flag_main) {
                $this->disableOldMainTemplate();
            }

            $settings = [];
            foreach ($this->getSettingsArguments() as $argument) {
                $settings[$argument] = $this->$argument;
            }

            if ($settings) {
                $this->settings = Json::encode($settings);
            }

            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        if ($this->settings) {
            $settings = Json::decode($this->settings);
            foreach ($settings as $argumentName => $argumentValue) {
                if (property_exists($this, $argumentName)) {
                    $this->$argumentName = $argumentValue;
                }
            }
        }

        parent::afterFind();
    }

    /**
     * Handle error exception.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param \Exception $e
     *
     * @return string
     */
    public static function errorExceptionHandler(\Exception $e)
    {
        $message = VarDumper::dumpAsString([
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        Yii::error($message, __METHOD__);

        return $message;
    }

    /**
     * Check if exist sibling main template.
     * If exist then replace main template flag.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @throws \Exception
     */
    public function disableOldMainTemplate()
    {
        $preMainTemplate = static::getMainTemplate($this->entity, $this->id);

        if ($preMainTemplate) {
            $preMainTemplate->flag_main = Template::FLAG_NONE;
            $preMainTemplate->skipBeforeSave = true;
            if (!$preMainTemplate->save(false, ['flag_main'])) {
                throw new \Exception(implode(', ', $preMainTemplate->getFirstErrors()));
            }
        }
    }

    public static function getMainTemplate($entity, $excludeId = null)
    {
        $query = self::find()
            ->where([
                'entity' => $entity,
                'flag_main' => self::FLAG_MAIN
            ]);

        if ($excludeId) {
            $query->andWhere(['<>', 'id', $excludeId]);
        }

        $mainTemplate = $query->one();

        return ($mainTemplate) ? : null;
    }

    /**
     * Render content by Twig.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param array $context
     * @param $attribute
     *
     * @return string
     */
    public function getCompiledContent(array $context, $attribute)
    {
        return Yii::$app->twig->getInstance()->render($this->$attribute, $context);
    }

    /**
     * Returns templates list by entity.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $entity
     *
     * @return array
     */
    public static function getListByEntity($entity)
    {
        $templates = static::find()
            ->where(['entity' => $entity])
            ->select(['id', 'name'])
            ->asArray()
            ->all();

        return ArrayHelper::map($templates, 'id', 'name');
    }
}
