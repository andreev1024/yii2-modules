<?php
use kartik\grid\GridView;
use kartik\icons\Icon;
use reseed\reWidgets\rebox\ReBox;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->translate->t('piwik tracking code list');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="piwik-index">
    <?php ReBox::begin([
        'header' => [
            'options' => [
                'class' => 'box-name',
                'title' => Html::encode($this->title)
            ],
            'icon' => [
                'name' => 'table',
                'framework' => 'fa',
                'options' => [],
                'space' => true,
                'tag' => 'i'
            ],
            'button' => [
                'tag' => 'a',
                'text' => Icon::show('plus'),
                'url' => yii\helpers\Url::to(['create']),
                'options' => [
                    'class' => 'box-icons',
                    'title' => Html::encode(Yii::$app->translate->t('Create new tracking code')),
                ],
            ],
        ],
    ]);

    $columns = [
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'siteId',
            'vAlign' => 'middle',
            'format' => 'raw',
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'scope',
            'vAlign' => 'middle',
            'format' => 'raw',
            'value' => function ($model) {
                return Yii::$app->translate->t($model['scope']);
            },
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'status',
            'vAlign' => 'middle',
            'format' => 'raw',
            'value' => function ($model) {
                $class = $model['status'] ? 'success' : 'danger';
                $label = $model['status'] ? 'enable' : 'disable';
                return "<span class='label label-{$class}'>" .
                    Yii::$app->translate->t($label) . '</span>';

            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        Url::to(['update', 'scope' => $model['scope']]),
                        [
                            'title' => Yii::$app->translate->t('update')
                        ]
                    );
                },
                'delete' => function($url, $model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-trash"></span>',
                        Url::to(['delete', 'scope' => $model['scope']]),
                        [
                            'title' => Yii::$app->translate->t('delete'),
                            'data' => [
                                'confirm' => Yii::$app->translate->t('Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]
                    );
                },
            ]
        ]
    ];

    $config = [
        'dataProvider' => $provider,
        'columns' => $columns,
        'condensed' => true,
        'responsive' => true,
        'export' => false,
        'hover' => true,
    ];
    ?>

    <?= GridView::widget($config) ?>

    <?php ReBox::end(); ?>
</div>
