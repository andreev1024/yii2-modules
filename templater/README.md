#Templater module.

This module provides the GUI for create template. You can use templates in your application for different purpose.

##Requirements

* "php": ">=5.4.0",
* "yiisoft/yii2": "*",
* "kartik-v/yii2-mpdf": "dev-master"

Also extension use `twig` which provided by the application.

##Configuration

- Add module to backend config file

```
'modules' => [
    'templater' => [
        'class' => 'andreev1024\templater\Module',
    ],
]
```

- Run migrations:

```
php yii migrate --migrationPath=@andreev1024/templater/migrations
```

## Explanation

* Config.php 
```
    static::ENTITY_BUY_ORDER_CONTRACT => [
        'class' => '\modules\buyOrder\models\BuyOrderContract',
        'type' => static::TYPE_PDF,
    ],
```

`type` determine which Model and GUI (CRUD) will be used for  current model.