<?php

use kartik\grid\GridView;
use kartik\icons\Icon;
use reseed\reWidgets\rebox\ReBox;
use andreev1024\sqs\models\SqsError;
use andreev1024\sqs\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;

YiiAsset::register($this);

$this->title = Yii::$app->translate->t('sqs errors', Module::$translateCategory);
$this->params['breadcrumbs'][] = $this->title;

//  selectors
$s = [
    'id' => [
        'addToQueue' => 'batch-add-to-queue',
        'batchDelete' => 'batch-delete',
        'grid' => 'gm-greed'
    ],
];

ReBox::begin([
    'header' => [
        'options' => [
            'class' => 'box-name',
            'title' => Html::encode($this->title),
        ],
        'icon' => [
            'name' => 'table',
            'framework' => 'fa',
            'options' => [],
            'space' => true,
            'tag' => 'i',
        ],
    ],
]);

$gridColumns = [
    [
        'class' => 'kartik\grid\ExpandRowColumn',
        'detailRowCssClass' => GridView::TYPE_DEFAULT,
        'value' => function ($model, $key, $index, $column) {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model, $key, $index, $column) {
            return Yii::$app->controller->renderPartial('_errorDetails', [
                'model' => $model,
            ]);
        },
    ],
    [
        'attribute' => 'sender_fault',
        'vAlign' => GridView::ALIGN_MIDDLE,
        'hAlign' => GridView::ALIGN_CENTER,
        'value' => function ($model, $key, $index, $widget) {
            if ($model->sender_fault) {
                $class = 'danger';
                $text = 'true';
            } else {
                $class = 'warning';
                $text = 'false';
            }

            return "<span class='label label-{$class}'>{$text}</span>";
        },
        'format' => 'raw',
        'filter' => false,
        'width' => '5%'
    ],
    [
        'attribute' => 'type',
        'vAlign' => 'middle',
        'value' => function ($model) {
            $types = SqsError::getTypeArray();
            return isset($types[$model->type]) ? $types[$model->type] : null;
        },
        'format' => 'raw',  //  IMPORTANT
        'filter' => SqsError::getTypeArray(),
    ],
    [
        'attribute' => 'code',
        'vAlign' => GridView::ALIGN_MIDDLE,
        'hAlign' => GridView::ALIGN_CENTER,
    ],
    [
        'attribute' => 'queue_url',
        'vAlign' => GridView::ALIGN_MIDDLE,
        'hAlign' => GridView::ALIGN_CENTER,
    ],
    [
        'attribute' => 'status',
        'value' => function ($model, $key, $index, $widget) {
           switch ($model->status) {
               case SqsError::STATUS_NEW:
                   $class = 'primary';
                   break;
               case SqsError::STATUS_FIXED:
                   $class = 'success';
                   break;
               default:
                   $class = 'default';
           }

           $statuses = SqsError::getStatusArray();
           $text = isset($statuses[$model->status]) ? $statuses[$model->status] : $model->status;
           return "<span class='label label-{$class}'>{$text}</span>";
        },
        'format' => 'raw',
        'vAlign' => GridView::ALIGN_MIDDLE,
        'hAlign' => GridView::ALIGN_CENTER,
        'filter' => false,
        'width' => '5%'
    ],
    [
        'attribute' => 'created_at',
        'vAlign' => GridView::ALIGN_MIDDLE,
        'hAlign' => GridView::ALIGN_CENTER,
    ],
    [
        'attribute' => 'updated_at',
        'vAlign' => GridView::ALIGN_MIDDLE,
        'hAlign' => GridView::ALIGN_CENTER,
    ],
    [
        'class' => '\kartik\grid\CheckboxColumn'
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'contentOptions' => ['style' => 'width:150px'],
        'vAlign' => GridView::ALIGN_MIDDLE,
        'header' => '',
        'dropdown' => false,
        'template' => '{update}'
    ],
];

$batchButtons =
    Html::a(
        Icon::show('gears', null, Icon::FA), Url::to(['add-to-queue']),
        [
            'id'=>$s['id']['addToQueue'],
            'class'=>'btn btn-primary',
            'title' =>Yii::$app->translate->t('add selected to queue', 'app'),
            'style' => 'margin-right:1em;'
        ]
    ) .
    Html::a(
        Icon::show('trash-o', null, Icon::FA), Url::to(['batch-delete']),
        [
            'id'=>$s['id']['batchDelete'],
            'class'=>'btn btn-default',
            'title' =>Yii::$app->translate->t('delete selected', 'app')
        ]
    );

echo GridView::widget([
    'id' => $s['id']['grid'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'condensed' => true,
    'responsive' => false,
    'export' => false,
    'hover' => true,
    'panel' => [
        'heading'=>false,
        'type'=>'success',
        'before'=>$batchButtons,
        'after'=>false,
        'footer'=>''    //  pagination
    ],
]);

ReBox::end();

$this->registerJs("
    $(document).on('click', '#{$s['id']['batchDelete']}', function (evt) {
        evt.preventDefault();
        var keys = $('#{$s['id']['grid']}').yiiGridView('getSelectedRows');
        if (!keys) {
            alert('" . Yii::$app->translate->t('you need to select at least one item.', 'app') . "');
        } else {
            if (confirm('" . Yii::$app->translate->t('are you sure you want to delete selected items?', 'app')  . "')) {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('href'),
                    data: {ids: keys}
                })
            }
        }
    });

    $(document).on('click', '#{$s['id']['addToQueue']}', function (evt) {
        evt.preventDefault();
        var keys = $('#{$s['id']['grid']}').yiiGridView('getSelectedRows');
        if (!keys) {
            alert('" . Yii::$app->translate->t('you need to select at least one item.', 'app') . "');
        } else {
            $.ajax({
                type: 'POST',
                url: $(this).attr('href'),
                data: {ids: keys}
            })
        }
    });
"
);
?>