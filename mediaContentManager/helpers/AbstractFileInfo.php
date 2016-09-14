<?php

namespace andreev1024\mediaContentManager\helpers;

abstract class AbstractFileInfo implements FileInfoInterface
{
    /** @var string */
    protected $filename;

    /** @var string */
    protected $name;

    /** @var string */
    protected $extension;

    /** @var int */
    protected $size;

    /** @var string */
    protected $type;

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @param mixed $file
     */
    public function __construct($file)
    {
        $this->init($file);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access protected
     *
     * @param mixed $file
     */
    abstract protected function init($file);
}
