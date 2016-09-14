<?php

namespace andreev1024\mediaContentManager\models;

use andreev1024\mediaContentManager\processors\ImageProcessor;
use yii\db\ActiveRecord;

/**
 * Class ImageOption
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @property string $type
 * @property int $width
 * @property int $height
 *
 * @package andreev1024\mediaContentManager\models
 */
class ImageOption extends ActiveRecord
{
    /**
     * @author Andreev <andreev1024@gmail.com>
     * @since Ver 1.0 added on 2015.05.14
     * @access public
     *
     * @return array
     */
    public static function getDefaultOptions()
    {
        return [
            ImageProcessor::SIZE_TYPE_SMALL => [
                'width' => 50,
                'height' => 50,
            ],
            ImageProcessor::SIZE_TYPE_MEDIUM => [
                'width' => 300,
                'height' => 300,
            ],
            ImageProcessor::SIZE_TYPE_LARGE => [
                'width' => 800,
                'height' => 800,
            ],
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @since Ver 1.0 added on 2015.05.14
     * @access public
     *
     * @return array
     */
    public static function getTypeLabels()
    {
        return [
            ImageProcessor::SIZE_TYPE_SMALL => \Yii::t('mediaContentModule', 'Small'),
            ImageProcessor::SIZE_TYPE_MEDIUM => \Yii::t('mediaContentModule', 'Medium'),
            ImageProcessor::SIZE_TYPE_LARGE => \Yii::t('mediaContentModule', 'Large'),
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @since Ver 1.0 added on 2015.05.14
     * @access public
     *
     * @param string $type
     *
     * @return \andreev1024\mediaContentManager\models\ImageOption|null
     */
    public static function getOption($type)
    {
        /** @var ImageOption $model */
        $model = static::findOne($type);
        if (empty($model) && in_array($type, ImageProcessor::getSizeTypes())) {
            $model = new self();
            $model->type = $type;
            $model->setDefaultValues();
        }

        return $model;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @since Ver 1.0 added on 2015.05.14
     * @access public
     *
     * @return ImageOption[]
     */
    public static function getAllOptions()
    {
        $options = static::find()->indexBy('type')->all();

        foreach (ImageProcessor::getSizeTypes() as $type) {
            if (!isset($options[$type])) {
                $option = new self();
                $option->type = $type;
                $option->setDefaultValues();

                $options[$type] = $option;
            }
        }

        rsort($options);

        return $options;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'width', 'height'], 'required'],
            [['type'], 'in', 'range' => ImageProcessor::getSizeTypes()],
            [['type'], 'unique'],
            [['width', 'height'], 'integer', 'integerOnly' => true],
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @since Ver 1.0 added on 2015.05.14
     * @access public
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return self::getTypeLabels()[$this->type];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @since Ver 1.0 added on 2015.05.14
     * @access public
     */
    public function setDefaultValues()
    {
        $this->setAttributes(self::getDefaultOptions()[$this->type]);
    }
}
