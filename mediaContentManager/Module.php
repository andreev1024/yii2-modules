<?php

namespace andreev1024\mediaContentManager;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Note:    if you going to add new storage class
 *          you must add it in $storageClasses.
 */
class Module extends \yii\base\Module
{
    /**
     * @var array Master storage config.
     */
    public $masterConfig;

    /**
     * @var string Master storage Id
     */
    public $masterStorage;

    /**
     * @var string For S3 it's bucket, for local - directory.
     */
    public $masterStorageDirectory;

    /**
     * @var array
     */
    public $slaveConfig;

    /**
     * @var string Slave storage Id
     */
    public $slaveStorage;

    /**
     * @var string
     */
    public $slaveStorageDirectory;

    /**
     * @var string Secure key for data encrypting.
     */
    public $key;

    /**
     * @var array Storage classes used in Module.
     */
    private static $storageClasses = [
        'andreev1024\mediaContentManager\components\S3Wrapper',
        'andreev1024\mediaContentManager\components\LocalStorage',
    ];

    /**
     * @var array
     */
    private static $_storageArray;

    /**
     * @var array
     */
    private $requiredArguments = [
        'masterStorage',
        'masterConfig',
        'masterStorageDirectory',
        'slaveStorage',
        'slaveStorageDirectory',
        'slaveConfig',
        'key',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        foreach ($this->requiredArguments as $reqArg) {
            if (!isset($this->$reqArg)) {
                throw new InvalidConfigException("Required argument `{$reqArg}` missed.");
            }
        }

        foreach ([$this->masterStorage, $this->slaveStorage] as $storage) {
            if (!in_array($storage, array_keys(static::getStorageArray()))) {
                throw new InvalidConfigException('Unsupported `storage` type.');
            }
        }

        foreach (['masterStorageDirectory', 'slaveStorageDirectory'] as $value) {
            $this->$value = Yii::getAlias($this->$value);
        }

        if (
            $this->masterStorage === $this->slaveStorage &&
            $this->masterStorageDirectory === $this->slaveStorageDirectory
        ) {
            throw new InvalidConfigException('Storage configurations must be different.');
        }
    }

    /**
     * Return Storage instance.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param string $storageId
     * @param string $storageDirectory
     * @param bool $enableSlave  If $enableSlave is `true` and storage config invalid
     *                           we use slave Storage.
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function getStorage($storageId, $storageDirectory, $enableSlave = true)
    {
        if (is_null($storageId) && is_null($storageDirectory)) {
            $storageId = $this->masterStorage;
            $storageDirectory = $this->masterStorageDirectory;
        }

        if (is_null($storageId) || is_null($storageDirectory)) {
            throw new InvalidParamException('`storageId` and `storageDirectory` must be set.');
        }

        if ($storageId !== $this->masterStorage && $storageId !== $this->slaveStorage) {
            throw new InvalidParamException('Invalid `Storage`.');
        }

        $allStorages = static::getStorageArray();
        try {
            Yii::trace('Try to save data.', __METHOD__);
            $config = array_merge(
                $this->getConfig($storageId, $storageDirectory),
                ['storageDirectory' => $storageDirectory]
            );
            $storage = new $allStorages[$storageId]['class'](['config' => $config]);
        } catch (\Exception $e) {
            Yii::warning($e, __METHOD__);
            if ($enableSlave && $storageId !== $this->slaveStorage) {
                $config = array_merge(
                    $this->slaveConfig,
                    ['storageDirectory' => $this->slaveStorageDirectory]
                );
                $storage = new $allStorages[$this->slaveStorage]['class'](['config' => $config]);
            } else {
                throw $e;
            }
        }
        return $storage;
    }

    /**
     * Return config by `storage`.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $storageId
     * @param $storageDirectory
     *
     * @return array
     * @throws InvalidConfigException
     */
    private function getConfig($storageId, $storageDirectory)
    {
        $storages = static::getStorageArray();
        $isMaster = $storageId === $this->masterStorage &&
            $storageDirectory === $this->masterStorageDirectory;

        $isSlave = $storageId === $this->slaveStorage &&
            $storageDirectory === $this->slaveStorageDirectory;

        if ($isMaster) {
            return $this->masterConfig;
        } elseif ($isSlave) {
            return $this->slaveConfig;
        } else {
            throw new InvalidConfigException(
                "Config for `{$storages[$storageId]['name']} ({$storageDirectory})` is not exist."
            );
        }
    }

    /**
     * Returns `storage` array.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getStorageArray()
    {
        if (!static::$_storageArray) {
            static::$_storageArray = [];
            foreach (self::$storageClasses as $class) {
                static::$_storageArray[$class::ID] = [
                    'name' => $class::NAME,
                    'class' => $class
                ];
            }
        }
        
        return static::$_storageArray;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $storageId
     * @param null $default
     *
     * @return null
     */
    public function getStorageById($storageId, $default = null)
    {
        $storages = static::getStorageArray();
        return isset($storages[$storageId]) ? $storages[$storageId] : $default;
    }
}
