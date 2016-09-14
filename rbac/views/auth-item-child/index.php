<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Auth Item Children';
$this->params['breadcrumbs'][] = ['url' => ['default/index'], 'label' => 'RBAC Module'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-child-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Auth Item Child', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-container', 'enablePushState' => false]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => \kartik\grid\EditableColumn::className(),
                'attribute' => 'parent',
                'editableOptions' => function () {
                    return [
                        'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                        'pjaxContainerId'=>'pjax-container',
                    ];
                },
                'refreshGrid' => true
            ],
            [
                'class' => \kartik\grid\EditableColumn::className(),
                'attribute' => 'child',
                'editableOptions' => function () {
                    return [
                        'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                        'pjaxContainerId'=>'pjax-container',
                    ];
                },
                'refreshGrid' => true
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end();?>
</div>
