<?php

namespace andreev1024\sqs\components;

/**
 * SQS stumb class (null object pattern).
 */
class SQSStumb implements SQSInterface
{
    /**
     * @inheritdoc
     */
    public function sendMessage($message, array $options)
    {
        // do nothing
    }

    /**
     * @inheritdoc
     */
    public function sendMessageBatch(array $messages, array $options)
    {
        // do nothing
    }
}
