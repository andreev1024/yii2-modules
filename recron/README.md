#Cron module.

This module provides a GUI for CRON commands management in Yii2 web app. 

##Configuration

- Add module to backend config file

```
'modules' => [
    'cron' => [
        'class' => 'andreev1024\recron\Module'
    ]
]
```

- If you want to allow access in _frontend_ (with token) you must add module to _frontend_ config file

```
'modules' => [
    'cron' => [
        'class' => 'andreev1024\recron\Module'
    ]
]
```

## Migrations:

At first time when you access CRON via web application it creates table re_cron

## How to use:

- Create new cron task at settings/index page in cron module
- After that in settings/apply module will fill crontab automatically