<?php

namespace andreev1024\mediaContentManager\components;

use andreev1024\s3\Storage;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Wrapper for S3 Component
 */
class S3Wrapper extends BaseStorage
{
    /**
     * Storage ID.
     */
    CONST ID = 1;

    /**
     * torage Name.
     */
    CONST NAME = 's3';

    /**
     * @var array
     */
    public $credentials;

    /**
     * @var string
     */
    public $bucket;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $defaultAcl;

    /**
     * @var string
     */
    public $policy;

    /**
     * @var string
     */
    public $version;

    /**
     * @var mixed CloudFront config
     */
    public $cloudFront;

    /**
     * @var \andreev1024\s3\Storage
     */
    private $storage;

    /**
     * Class init.
     *
     * @author Andreev <andreev1024@gmail.com>
     */
    public function init()
    {
        parent::init();
        $this->validateConfig($this->config);
        $this->setAttributes($this->config);
        $this->storage = new Storage([
            'credentials' => $this->credentials,
            'bucket' => $this->bucket,
            'defaultAcl' => $this->defaultAcl,
            'region' => $this->region,
            'version' => $this->version,
            'cloudFrontConfig' => $this->cloudFront,
        ]);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     * @param $data
     *
     * @return mixed
     */
    public function put($fileName, $data)
    {
        return $this->storage->put($fileName, $data);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     *
     * @return mixed
     */
    public function delete($fileName)
    {
        return $this->storage->delete($fileName);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     *
     * @return mixed
     */
    public function getUrl($fileName)
    {
        if (isset($this->cloudFront) && $this->cloudFront) {
            if ($this->access !== self::ACCESS_PUBLIC) {
                $url = $this->storage->getCloudFrontSignedUrl($fileName);
            } else {
                $url = $this->storage->getCloudFrontUrl($fileName);
            }
        } else {
            $url = $this->storage->getUrl($fileName);
        }
        return $url;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @param $fileName
     * @param $data
     *
     * @return mixed
     */
    public function upload($fileName, $data)
    {
        return $this->storage->upload($fileName, $data);
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public function getStorageDirectory()
    {
        return $this->bucket;
    }

    /**
     * Checks Storage base config params.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $config
     *
     * @throws InvalidConfigException
     */
    public function validateConfig($config)
    {
        if (!$config) {
            throw new InvalidConfigException('Wrong component configuration.');
        }

        if (!isset($config['credentials']['key']) || !isset($config['credentials']['secret'])) {
            throw new InvalidConfigException('`' . self::NAME . '` invalid config.');
        }
    }

    /**
     * Sets Storage attributes.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $attributes
     */
    public function setAttributes($attributes)
    {
        $this->bucket = Yii::getAlias($attributes['storageDirectory']);
        unset($attributes['storageDirectory']);

        foreach ($attributes as $attributeName => $attributeValue) {
            $this->$attributeName = $attributeValue;
        }
    }
}