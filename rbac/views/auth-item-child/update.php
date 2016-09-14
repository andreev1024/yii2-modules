<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model andreev1024\rbac\models\AuthItemChild */

$this->title = 'Update Auth Item Child: ' . ' ' . $model->parent;
$this->params['breadcrumbs'][] = ['url' => ['default/index'], 'label' => 'RBAC Module'];
$this->params['breadcrumbs'][] = ['label' => 'Auth Item Children', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->parent, 'url' => ['view', 'parent' => $model->parent, 'child' => $model->child]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-item-child-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
