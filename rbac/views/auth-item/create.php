<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model andreev1024\rbac\models\AuthItem */

$this->title = 'Create Auth Item';
$this->params['breadcrumbs'][] = ['url' => ['default/index'], 'label' => 'RBAC Module'];
$this->params['breadcrumbs'][] = ['label' => 'Auth Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
