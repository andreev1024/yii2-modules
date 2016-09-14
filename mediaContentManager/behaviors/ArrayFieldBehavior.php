<?php

namespace andreev1024\mediaContentManager\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class ArrayFieldBehavior
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\behaviors
 */
class ArrayFieldBehavior extends Behavior
{
    /** @var array */
    public $attributes = [];

    /** @var mixed */
    public $emptyEncodedValue;

    /** @var mixed */
    public $emptyDecodedValue;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'decode',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'encode',
            ActiveRecord::EVENT_BEFORE_INSERT => 'encode',
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     */
    public function encode()
    {
        foreach ($this->attributes as $attribute) {
            $value = $this->owner->{$attribute};
            $value = !empty($value) ? Json::encode($value) : $this->emptyEncodedValue;

            $this->owner->{$attribute} = $value;
        }
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     */
    public function decode()
    {
        foreach ($this->attributes as $attribute) {
            $value = Json::decode($this->owner->{$attribute});

            $this->owner->{$attribute} = !empty($value) ? $value : $this->emptyDecodedValue;
        }
    }
}
