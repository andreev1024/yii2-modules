<?php

namespace andreev1024\sqs\components;

use Aws\Sdk;
use andreev1024\sqs\models\SqsError;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * Wrapper for AWS SQS.
 */
class SQSWrapper implements SQSInterface
{
    /**
     * @var \Aws\Sqs\SqsClient
     */
    private $_client;

    /**
     * @var Sdk
     */
    private $sdk;

    // http://docs.aws.amazon.com/AWSSimpleQueueService/latest/APIReference/API_SendMessageBatch.html
    private $sqsSendBatchLimit = 10;

    /**
     * @var array
     */
    public $config;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->config = $options['config'];
        $this->sdk = new Sdk($this->config);
        $this->_client = $this->sdk->createSqs();
    }

    /**

     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $message
     * @param array $options
     */

    /**
     * Send a message to queue.
     *
     * If message send failed client throws Exception (see AWS `SendMessage` description)
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $message
     * @param array $options
     *
     * @return array
     * @throws \Exception
     */
    public function sendMessage($message, array $options)
    {
        foreach ($options['queues'] as $queueUrl) {
            $this->_client->SendMessage([
                'MessageBody' => is_array($message) ? Json::encode($message) : $message,
                'QueueUrl' => $queueUrl
            ]);
        }

        return ['success' => true];
    }

    /**
     * Batch send messages to queue.
     *
     * If some of messages send failed we try to seve them to database.
     * If they saved - we returned array with saved error ids.
     * If don't - we throw Exception.
     *
     * Also AWS SendMessageBatch() can throw Exception (see AWS `SendMessageBatch` description).
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param array $messages
     * @param array $options
     *
     * @return array
     * @throws \Exception
     */
    public function sendMessageBatch(array $messages, array $options)
    {
        $response = ['success' => true];

        if (!$messages) {
            return $response;
        }

        foreach ($options['queues'] as $queueUrl) {
            foreach(array_chunk($messages, $this->sqsSendBatchLimit) as $batchKey => $batch) {
                $entries = [];
                foreach ($batch as $messageKey => $oneMessage) {
                    $id = "{$batchKey}{$messageKey}";
                    $entries[$id] = [
                        'Id' => $id,
                        'MessageBody' => is_array($oneMessage) ? Json::encode($oneMessage) : $oneMessage,
                    ];
                }

                $sqsResponse = $this->_client->SendMessageBatch([
                    'Entries' => $entries,
                    'QueueUrl' => $queueUrl
                ])->toArray();

                if (isset($sqsResponse['Failed'])) {
                    $response['success'] = false;
                    $response['failed'] = [];
                    foreach ($sqsResponse['Failed'] as $item) {

                        $sqsError = new SqsError([
                            'sender_fault' => $item['SenderFault'],
                            'code' => $item['Code'],
                            'error_message' => $item['Message'],
                            'metadata' => Json::encode($sqsResponse['@metadata']),
                            'message' => $entries[$item['Id']]['MessageBody'],
                            'queue_url' => $queueUrl,
                            'type' => SqsError::TYPE_SEND_ERROR,
                        ]);

                        if (!$sqsError->save()) {
                            throw new InvalidParamException('SQS: Send message failed. Save error failed.');
                        }

                        $response['failed'][] = $sqsError->id;
                    }
                }
            }
        }

        return $response;
    }

    /**
     * @author Andreev <andreev1024@gmail.com>
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }
}
