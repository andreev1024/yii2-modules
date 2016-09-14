<?php

namespace andreev1024\mediaContentManager\helpers;

use yii\web\UploadedFile;

/**
 * Class UploadedFileInfo
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\helpers
 */
class UploadedFileInfo extends AbstractFileInfo
{
    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @param \yii\web\UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        parent::__construct($file);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @param \yii\web\UploadedFile $file
     */
    protected function init($file)
    {
        $this->filename = $file->tempName;
        $this->name = $file->name;
        $this->extension = $file->getExtension();
        $this->size = $file->size;
        $this->type = $file->type;
    }
}
