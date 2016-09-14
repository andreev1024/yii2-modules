<?php

namespace andreev1024\mediaContentManager\models\scopes;

use andreev1024\mediaContentManager\models\File;
use yii\db\ActiveQuery;

/**
 * Class FileQuery is the Query class for File model.
 */
class FileQuery extends ActiveQuery
{
    /**
     * Add 'image' criteria.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return $this
     */
    public function image()
    {
        $this->andWhere(['processing_type' => File::PROCESSING_TYPE_IMAGE]);
        return $this;
    }
}
