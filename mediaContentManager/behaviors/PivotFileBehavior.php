<?php

namespace andreev1024\mediaContentManager\behaviors;

use andreev1024\mediaContentManager\models\File;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class PivotFileBehavior
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @property array $pivot
 * @property File[] $files
 *
 * @package andreev1024\mediaContentManager\behaviors
 */
class PivotFileBehavior extends Behavior
{
    /** @var ActiveRecord */
    public $owner;

    /** @var string */
    public $pivotClassName;

    /** @var array */
    public $pivotLink;

    /** @var array|string */
    public $pivotOnCondition;

    /** @var array */
    public $link;

    /** @var bool */
    public $deleteOnUnlink = true;

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.05.21
     * @access public
     *
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'unlinkAll',
        ];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.04.05
     * @access public
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPivot()
    {
        $query = $this->owner->hasMany($this->pivotClassName, $this->pivotLink);

        if (!empty($this->pivotOnCondition)) {
            $query->andOnCondition($this->pivotOnCondition);
        }

        return $query;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.04.05
     * @access public
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->owner->hasMany(File::className(), $this->link)->via('pivot');
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added at 2015.05.21
     * @access public
     */
    public function unlinkAll()
    {
        $this->owner->unlinkAll('files', $this->deleteOnUnlink);
    }
}
