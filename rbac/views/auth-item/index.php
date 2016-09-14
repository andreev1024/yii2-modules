<?php

use yii\helpers\Html;
use yii\grid\GridView;
use andreev1024\rbac\models\AuthItem;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Auth Items';
$this->params['breadcrumbs'][] = ['url' => ['default/index'], 'label' => 'RBAC Module'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Auth Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'label' => 'type',
                'value' => function($model){
                        if($model->type == AuthItem::TYPE_PERMISSION)
                            return 'Permission';
                        elseif($model->type == AuthItem::TYPE_ROLE)
                            return 'Role';
                    }
            ],
            'description:ntext',
            'rule_name',
            //'data:ntext',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
