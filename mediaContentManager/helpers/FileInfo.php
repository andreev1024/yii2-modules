<?php

namespace andreev1024\mediaContentManager\helpers;

use yii\helpers\FileHelper;

/**
 * Class FileInfo
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\helpers
 */
class FileInfo extends AbstractFileInfo
{
    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access protected
     *
     * @param string $file
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function init($file)
    {
        $this->filename = $file;
        $this->name = pathinfo($file, PATHINFO_BASENAME);
        $this->extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $this->size = filesize($file);
        $this->type = FileHelper::getMimeType($file);
    }
}
