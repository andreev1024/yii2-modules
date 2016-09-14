<?php

namespace andreev1024\mediaContentManager\processors;

use andreev1024\mediaContentManager\models\File;
use andreev1024\mediaContentManager\Module;
use yii\base\InvalidConfigException;

/**
 * Class Builder
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\processors
 */
class Builder
{
    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @param string $filename
     * @param bool $async
     * @param array $storageConfig
     * @param integer $type
     *
     * @return Processor
     */
    public static function build($filename, $async, array $storageConfig, $type = null)
    {
        switch ($type) {
            case File::PROCESSING_TYPE_IMAGE:
                $className = ImageProcessor::className();
                break;
            default:
                $className = Processor::className();
        }

        $storage = Module::getInstance()->getStorage(
            $storageConfig['storage'],
            $storageConfig['storageDirectory'],
            $storageConfig['useDefault']
        );

        return \Yii::$container->get($className, [$filename, $async, $storage]);
    }
}
