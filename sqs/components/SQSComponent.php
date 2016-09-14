<?php

namespace andreev1024\sqs\components;

use yii\base\Component;

/**
 * SQS Component class.
 * @package andreev1024\sqs
 */
class SQSComponent extends Component implements SQSInterface
{
    /**
     * @var array ['key' => ..., 'secret' => ...]
     */
    public $credentials;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $version;

    /**
     * @var bool If true - component must return SQSStumb
     */
    public $stumb = false;

    /**
     * @var SQSInterface
     */
    private $_client;

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return SQSInterface
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->_client) {
            if($this->stumb) {
                $this->_client = new SQSStumb;
            } elseif ($this->credentials && $this->region && $this->version) {
                $this->_client = new SQSWrapper([
                    'config' => [
                        'credentials' => $this->credentials,
                        'region' => $this->region,
                        'version' => $this->version,
                    ]
                ]);
            } else {
                throw new InvalidConfigException('Invalid config for AWS SQS');
            }
        }

        return $this->_client;
    }

    /**
     * @inheritdoc
     */
    public function sendMessage($message, array $options)
    {
        return $this->_client->sendMessage($message, $options);
    }

    /**
     * @inheritdoc
     */
    public function sendMessageBatch(array $messages, array $options)
    {
        return $this->_client->sendMessageBatch($messages, $options);
    }
}
