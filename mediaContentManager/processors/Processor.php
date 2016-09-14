<?php

namespace andreev1024\mediaContentManager\processors;

use andreev1024\mediaContentManager\components\BaseStorage;
use yii\base\Configurable;

/**
 * Class Processor
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 *
 * @package andreev1024\mediaContentManager\processors
 */
class Processor implements Configurable
{
    /** @var BaseStorage */
    protected $storage;

    /** @var string */
    protected $filename;

    /**
     * @var bool Do some process async.
     *           For example, you can create thumbnails async (in background).
     */
    protected $async;

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.05
     * @access public
     *
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     *
     * @param string $filename
     * @param bool $async
     * @param BaseStorage $storage
     */
    public function __construct($filename, $async, BaseStorage $storage)
    {
        $this->filename = $filename;
        $this->storage = $storage;
        $this->async = $async;
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
        $this->storage->upload(
            $this->getFilename(),
            file_get_contents($filename)
        );
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     */
    public function delete()
    {
        $this->storage->delete($this->getFilename());
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->storage->getUrl($this->getFilename());
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.04.10
     * @access public
     *
     * @return string
     */
    protected function getFilename()
    {
        return $this->filename;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return BaseStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
