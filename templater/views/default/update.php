<?php
use yii\helpers\Html;

$this->title = Yii::$app->translate->t('Update {modelClass}: ', 'app', [
    'modelClass' => 'Template',
]) . ' ' . $model->name;

$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->translate->t('Templates', 'app'),
    'url' => ['index']
];

$this->params['breadcrumbs'][] = ['label' => $model->name];
$this->params['breadcrumbs'][] = Yii::$app->translate->t('Update', 'app');

$data =  [
    'model' => $model,
    'title' => $this->title,
    'variableList' => $variableList,
    'type' => $type,
    'error' => $error,
];
?>

<div class="template-update">
    <?= $this->render("_form", $data) ?>
</div>
