<?php

namespace andreev1024\mediaContentManager\helpers;

/**
 * Interface FileInfoInterface
 *
 * @author Andreev <andreev1024@gmail.com>
 * @since 1.0
 * 
 * @package andreev1024\mediaContentManager\helpers
 */
interface FileInfoInterface
{
    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getFilename();

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getName();

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getExtension();

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return int
     */
    public function getSize();

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @version Ver 1.0 added on 2015.05.21
     * @access public
     *
     * @return string
     */
    public function getType();
}
