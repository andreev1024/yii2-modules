Rbac Module
================================

Install
-------------------

Add in common/config/main.php or main-local.php


```
#!php

'rbac' => [
    ...
    'rbac' => [
        'class' => 'rbac\rbac\Module',
    ],
],
'components' => [
    'authManager' => [
		'class' => \rbac\rbac\components\RbacManager::className()
		...
	]
    ...

```