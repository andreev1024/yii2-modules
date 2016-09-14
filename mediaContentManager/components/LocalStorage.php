<?php

namespace andreev1024\mediaContentManager\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * LocalStorage save data in local files.
 */
class LocalStorage extends BaseStorage
{
    /**
     * Storage ID.
     */
    CONST ID = 0;

    /**
     * Storage Name.
     */
    CONST NAME = 'local';

    /**
     * @var string Directory where we save data.
     */
    public $directory;

    /**
     * @var Url for getting saved data.
     */
    public $url;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->validateConfig($this->config);
        $this->setAttributes($this->config);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     * @param $data
     *
     * @return int
     * @throws \yii\base\Exception
     */
    public function put($fileName, $data)
    {
        $fileName = rtrim($this->directory, '/') . '/' . trim($fileName, '/');
        $dirName = dirname($fileName);
        FileHelper::createDirectory($dirName);
        return file_put_contents ($fileName, $data);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     *
     * @return bool
     */
    public function delete($fileName)
    {
        $fileName = rtrim($this->directory, '/') . '/' . trim($fileName, '/');
        if (file_exists($fileName)) {
            return unlink($fileName);
        }
        return false;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     *
     * @return string
     */
    public function getUrl($fileName)
    {
        return rtrim($this->url, '/') . '/' . trim($fileName, '/');
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     * @param $data
     *
     * @return null
     */
    public function upload($fileName, $data)
    {
        $this->put($fileName, $data);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function getStorageDirectory()
    {
        return $this->directory;
    }

    /**
     * Checks Storage base config params.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $config
     *
     * @throws InvalidConfigException
     * @return null
     */
    public function validateConfig($config)
    {
        if (!$config) {
            throw new InvalidConfigException('Wrong component configuration.');
        }

        if (!isset($config['url'])) {
            throw new InvalidConfigException('`' . self::NAME . '` invalid config.');
        }
    }

    /**
     * Sets Storage attributes.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $attributes
     *
     * @throws InvalidConfigException
     * @return null
     */
    public function setAttributes($attributes)
    {
        $this->directory = Yii::getAlias($attributes['storageDirectory']);
        $this->url = Yii::getAlias($attributes['url']);

        if (!file_exists($this->directory)) {
            throw new InvalidConfigException("`{$this->directory}` doesn't exist.");
        }

        if (!is_writable($this->directory)) {
            throw new InvalidConfigException("`{$this->directory}` doesn't writable.");
        }
    }
}
