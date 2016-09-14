<?php

$this->title = Yii::$app->translate->t('create tracking code');
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->translate->t('piwik tracking code list'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_form', [
    'title' => $this->title,
    'model' => $model
]);