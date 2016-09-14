<?php

namespace andreev1024\sqs\components;

/**
 * Interface for SQS components.
 */
interface SQSInterface
{
    /**
     * Send a message to queue.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $message
     * @param array $options
     *
     * @return mixed
     */
    public function sendMessage($message, array $options);

    /**
     * Batch send messages to queue.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param array $messages
     * @param array $options
     *
     * @return mixed
     */
    public function sendMessageBatch(array $messages, array $options);
}
