<?php

namespace andreev1024\mediaContentManager\components;

use yii\base\Component;

/**
 * Base abstract class for MediaContent Storages.
 */
abstract class BaseStorage extends Component
{
    const ACCESS_PUBLIC = 1;

    const ACCESS_PRIVATE = 0;

    /**
     * @var string Access level.
     */
    public $access = self::ACCESS_PUBLIC;

    /**
     * @var Storage config.
     */
    public $config;

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     * @param $data
     *
     * @return mixed
     */
    abstract public function put($fileName, $data);

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     *
     * @return mixed
     */
    abstract public function delete($fileName);

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     *
     * @return mixed
     */
    abstract public function getUrl($fileName);

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     * @param $data
     *
     * @return mixed
     */
    abstract public function upload($fileName, $data);

    /**
     * Returns storage directory.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return mixed
     */
    abstract public function getStorageDirectory();

    /**
     * Checks Storage base config params.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $config
     *
     * @return mixed
     */
    abstract public function validateConfig($config);

    /**
     * Sets Storage attributes.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $attributes
     *
     * @return mixed
     */
    abstract public function setAttributes($attributes);

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return mixed
     */
    public function getId()
    {
        return static::ID;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return mixed
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Returns `storage access` array.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getStorageAccessArray()
    {
        return [
            self::ACCESS_PRIVATE => 0,
            self::ACCESS_PUBLIC => 1,
        ];
    }
}