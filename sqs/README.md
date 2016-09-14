#Amazon SQS Component for Yii2

##Installing

*   Add in config:
```
...
'modules' => [
    'sqs' => [
        'class' => 'andreev1024\sqs\Module',
    ]
]
...
'components' => [
       ...
        'sqs' => [
            'class' => 'andreev1024\sqs\components\SQSComponent',
            'stumb' => false,
            'credentials' => [
                'key' => 'I'am a key',
                'secret' => 'I'am a secret',
            ],
            'region' => 'I'am a region',
            'version' => 'I'am a version',
        ]
        ...
    ],
```

*   perform migrations from `migrations` directory;

###How to use

*   You can get SQS client from anywhere

```
    Yii::$app->sqs;
```

*   You can send a mesage (`sendMessage()`) or messages (`sendMessageBatch()`).

*   Sometimes your message send can failed. `sendMessage`, `sendMessageBatch` throw exceptions, return some results etc. Check method description for details. Keep in mind that you must take care about handle errors. For e.g.:
```
    try {
        $sqsClient->sendMessage($message, $options);
    } catch (\Exception $e) {
        handleSendExeption($e);
    }
```

*  Because the batch request can result in a combination of successful and unsuccessful actions, we save `failed` message in database. You can check these message from GUI (`/sqs/error/index`). There you can fix messages and re-send them.

###What else

*   What is `stumb` option ?

    Often, when you develop app, you don't have credentials or they invalid. You will get an error. To prevent this behavior you can set 'stumb' to `true`.
