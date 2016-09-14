<?php

namespace andreev1024\mediaContentManager\processors;

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use andreev1024\mediaContentManager\models\ImageOption;
use Yii;
use yii\imagine\Image;

/**
 * Class ImageProcessor
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\processors
 */
class ImageProcessor extends Processor
{
    const SIZE_TYPE_SMALL = 'small';
    const SIZE_TYPE_MEDIUM = 'medium';
    const SIZE_TYPE_LARGE = 'large';

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     *
     * @return array
     */
    public static function getSizeTypes()
    {
        return [
            self::SIZE_TYPE_SMALL,
            self::SIZE_TYPE_MEDIUM,
            self::SIZE_TYPE_LARGE,
        ];
    }

    /**
     * @param string $sizeType
     * @param BoxInterface $size
     *
     * @return array
     */
    protected static function getCalculatedSize($sizeType, BoxInterface $size)
    {
        $option = ImageOption::getOption($sizeType);
        $width = intval($option->width);
        $height = intval($option->height);

        if (!$width && !$height) {
            $ratio = self::getRatioByOriginal($sizeType);
            $height = $ratio * $size->getHeight();
            $width = $ratio * $size->getWidth();
        } elseif (!$width) {
            $ratio = $height / $size->getHeight();
            $width = $ratio * $size->getWidth();
        } elseif (!$height) {
            $ratio = $width / $size->getWidth();
            $height = $ratio * $size->getHeight();
        }

        return [
            'width' => intval($width),
            'height' => intval($height)
        ];
    }

    /**
     * @param string $sizeType
     *
     * @return mixed
     */
    protected static function getRatioByOriginal($sizeType)
    {
        $ratio = [
            self::SIZE_TYPE_SMALL => 0.3,
            self::SIZE_TYPE_MEDIUM => 0.6,
            self::SIZE_TYPE_LARGE => 0.9,
        ];

        return $ratio[$sizeType];
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     *
     * @param string $filename
     */
    public function upload($filename)
    {
        parent::upload($filename);
        if (!$this->async) {
            $this->createThumbnails($filename);
        }
    }

    /**
     * Creates thumbnails and save them in storage
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $filePath
     */
    public function createThumbnails($filePath)
    {
        $original = Image::frame($filePath);

        foreach (self::getSizeTypes() as $type) {
            $size = self::getCalculatedSize($type, $original->getSize());
            
            $image = Image::frame($filePath);
            $image->resize(new Box($size['width'], $size['height']));

            $this->storage->put(
                $this->getFilename($type),
                (string)$image
            );
        }
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     */
    public function delete()
    {
        parent::delete();

        foreach (self::getSizeTypes() as $type) {
            $this->storage->delete($this->getFilename($type));
        }
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     *
     * @param string $sizeType
     *
     * @return string
     */
    public function getUrl($sizeType = null)
    {
        return $this->storage->getUrl($this->getFilename($sizeType));
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access protected
     *
     * @param string $sizeType
     *
     * @return string
     */
    protected function getFilename($sizeType = null)
    {
        if (empty($sizeType)) {
            return parent::getFilename();
        }

        $path = pathinfo($this->filename, PATHINFO_DIRNAME);
        $name = pathinfo($this->filename, PATHINFO_BASENAME);

        return $path . '/' . $sizeType . '_' . $name;
    }
}
