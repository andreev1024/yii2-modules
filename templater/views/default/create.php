<?php
use yii\helpers\Html;

$this->title = Yii::$app->translate->t('Create Template', 'app');
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->translate->t('Templates', 'app'),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

$data =  [
    'model' => $model,
    'title' => $this->title,
    'variableList' => $variableList,
    'type' => $type,
    'error' => $error,
];

?>
<div class="template-create">
    <?= $this->render("_form", $data) ?>
</div>
