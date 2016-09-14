<?php

namespace andreev1024\mediaContentManager\behaviors;

use andreev1024\mediaContentManager\models\File;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Class FileBehavior
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\behaviors
 */
class FileBehavior extends Behavior
{
    /** @var ActiveRecord */
    public $owner;

    /** @var string */
    public $attribute;

    /** @var string|callable */
    public $path;

    /** @var string */
    public $relation;

    /** @var bool */
    public $async;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'save',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'save',
            ActiveRecord::EVENT_BEFORE_DELETE => 'delete',
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.05.21
     * @access public
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (empty($this->attribute)) {
            throw new InvalidConfigException('FileBehavior::$attribute isn\'t set.');
        }

        if (empty($this->relation)) {
            throw new InvalidConfigException('FileBehavior::$relation isn\'t set.');
        }
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.05.21
     * @access public
     *
     * @return bool
     */
    public function save()
    {
        $attachment = $this->owner->{$this->attribute};
        if ($attachment instanceof UploadedFile) {
            $path = is_callable($this->path) ? call_user_func($this->path) : $this->path;

            $model = new File();
            $model->setAttributes([
                'path' => $path,
                'file' => $attachment,
            ]);

            if (isset($this->async)) {
                $model->async = $this->async;
            }

            if (!$model->save()) {
                return false;
            }

            $this->owner->unlinkAll($this->relation, true);
            $this->owner->{$this->getRelationForeignKey()} = $model->getPrimaryKey();
        }

        return true;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.05.21
     * @access public
     */
    public function delete()
    {
        $this->owner->unlinkAll($this->relation, true);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.05.22
     * @access protected
     *
     * @return string
     */
    protected function getRelationForeignKey()
    {
        $relation = $this->owner->getRelation($this->relation);

        return current($relation->link);
    }
}
